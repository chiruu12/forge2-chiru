import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { getTicket, updateTicket } from './api';
import { createComment } from '../comments/api';
import StatusPill from '../../components/StatusPill';
import PriorityPill from '../../components/PriorityPill';
import Spinner from '../../components/Spinner';
import { useAuth } from '../../lib/auth.jsx';

export default function TicketDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [ticket, setTicket] = useState(null);
  const [loading, setLoading] = useState(true);
  const [commentBody, setCommentBody] = useState('');
  const [isInternal, setIsInternal] = useState(false);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    async function load() {
      try {
        const res = await getTicket(id);
        setTicket(res.data);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [id]);

  async function handleAddComment(e) {
    e.preventDefault();
    if (!commentBody.trim()) return;
    setSaving(true);
    try {
      const res = await createComment(id, { body: commentBody, is_internal: isInternal });
      setTicket((prev) => ({
        ...prev,
        comments: [...(prev.comments || []), res.data],
      }));
      setCommentBody('');
      setIsInternal(false);
    } catch (err) {
      console.error(err);
    } finally {
      setSaving(false);
    }
  }

  async function handleStatusChange(newStatus) {
    try {
      const res = await updateTicket(id, { status: newStatus });
      setTicket(res.data);
    } catch (err) {
      console.error(err);
    }
  }

  if (loading) return <Spinner />;
  if (!ticket) return <div className="p-6 text-red-600">Ticket not found</div>;

  const canUpdate = user?.role === 'admin' || user?.role === 'agent' || ticket.requester_id === user?.id;

  return (
    <div className="p-6 max-w-4xl">
      <button onClick={() => navigate('/tickets')} className="text-sm text-blue-600 hover:underline mb-4">
        ← Back to tickets
      </button>

      <div className="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <div className="flex items-start justify-between mb-4">
          <div>
            <h1 className="text-xl font-bold text-slate-800">{ticket.subject}</h1>
            <p className="text-sm text-slate-500 mt-1">
              Created {new Date(ticket.created_at).toLocaleString()} by {ticket.requester?.name}
            </p>
          </div>
          <div className="flex gap-2">
            <StatusPill status={ticket.status} />
            <PriorityPill priority={ticket.priority} />
          </div>
        </div>

        <div className="prose max-w-none text-slate-700 mb-4">
          {ticket.description || 'No description provided.'}
        </div>

        <div className="flex gap-6 text-sm text-slate-600 border-t border-slate-100 pt-4">
          <div>
            <span className="font-medium">Assignee:</span>{' '}
            {ticket.assignee?.name || 'Unassigned'}
          </div>
          <div>
            <span className="font-medium">Requester:</span>{' '}
            {ticket.requester?.name}
          </div>
        </div>

        {canUpdate && (
          <div className="mt-4 flex gap-2">
            {['open', 'in_progress', 'pending', 'resolved', 'closed'].map((s) => (
              <button
                key={s}
                onClick={() => handleStatusChange(s)}
                disabled={ticket.status === s}
                className={`px-3 py-1 rounded-lg text-xs font-medium transition ${
                  ticket.status === s
                    ? 'bg-slate-800 text-white'
                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                }`}
              >
                {s.replace('_', ' ')}
              </button>
            ))}
          </div>
        )}
      </div>

      <div className="bg-white rounded-xl border border-slate-200 p-6">
        <h2 className="text-lg font-semibold text-slate-800 mb-4">Comments</h2>
        <div className="space-y-4 mb-6">
          {(ticket.comments || []).length === 0 ? (
            <p className="text-slate-400 text-sm">No comments yet.</p>
          ) : (
            ticket.comments.map((c) => (
              <div key={c.id} className={`p-3 rounded-lg ${c.is_internal ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50'}`}>
                <div className="flex items-center justify-between mb-1">
                  <span className="font-medium text-sm text-slate-700">{c.author?.name}</span>
                  <span className="text-xs text-slate-400">
                    {new Date(c.created_at).toLocaleString()}
                  </span>
                </div>
                {c.is_internal && (
                  <span className="text-xs font-medium text-amber-600 mb-1 block">Internal note</span>
                )}
                <p className="text-sm text-slate-700">{c.body}</p>
              </div>
            ))
          )}
        </div>

        <form onSubmit={handleAddComment} className="space-y-3">
          <textarea
            value={commentBody}
            onChange={(e) => setCommentBody(e.target.value)}
            placeholder="Add a comment..."
            className="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 min-h-[80px]"
            required
          />
          <div className="flex items-center justify-between">
            <label className="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
              <input
                type="checkbox"
                checked={isInternal}
                onChange={(e) => setIsInternal(e.target.checked)}
                className="rounded border-slate-300"
              />
              Internal note
            </label>
            <button
              type="submit"
              disabled={saving}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 transition"
            >
              {saving ? 'Posting...' : 'Post comment'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
