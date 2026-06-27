import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getTickets } from './api';
import StatusPill from '../../components/StatusPill';
import PriorityPill from '../../components/PriorityPill';
import Spinner from '../../components/Spinner';
import EmptyState from '../../components/EmptyState';

const statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
const statusLabels = {
  open: 'Open',
  in_progress: 'In Progress',
  pending: 'Pending',
  resolved: 'Resolved',
  closed: 'Closed',
};

export default function TicketBoard() {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState('list'); // 'list' | 'board'
  const [filters, setFilters] = useState({ status: '', priority: '', q: '' });

  useEffect(() => {
    async function load() {
      try {
        const params = {};
        if (filters.status) params.status = filters.status;
        if (filters.priority) params.priority = filters.priority;
        if (filters.q) params.q = filters.q;
        const res = await getTickets(params);
        setTickets(res.data || []);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [filters]);

  if (loading) return <Spinner />;

  const grouped = {};
  statusOrder.forEach((s) => (grouped[s] = []));
  tickets.forEach((t) => {
    if (grouped[t.status]) grouped[t.status].push(t);
  });

  return (
    <div className="p-6">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-slate-800">Tickets</h1>
        <div className="flex gap-2">
          <button
            onClick={() => setView('list')}
            className={`px-3 py-1 rounded-lg text-sm font-medium ${view === 'list' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'}`}
          >
            List
          </button>
          <button
            onClick={() => setView('board')}
            className={`px-3 py-1 rounded-lg text-sm font-medium ${view === 'board' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'}`}
          >
            Board
          </button>
        </div>
      </div>

      <div className="flex flex-wrap gap-3 mb-4">
        <input
          type="text"
          placeholder="Search..."
          value={filters.q}
          onChange={(e) => setFilters((f) => ({ ...f, q: e.target.value }))}
          className="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select
          value={filters.status}
          onChange={(e) => setFilters((f) => ({ ...f, status: e.target.value }))}
          className="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Status</option>
          {statusOrder.map((s) => (
            <option key={s} value={s}>{statusLabels[s]}</option>
          ))}
        </select>
        <select
          value={filters.priority}
          onChange={(e) => setFilters((f) => ({ ...f, priority: e.target.value }))}
          className="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Priority</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
      </div>

      {view === 'list' ? (
        <div className="bg-white rounded-xl border border-slate-200 overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-slate-50 border-b border-slate-200">
              <tr>
                <th className="text-left px-4 py-3 font-medium text-slate-600">Subject</th>
                <th className="text-left px-4 py-3 font-medium text-slate-600">Status</th>
                <th className="text-left px-4 py-3 font-medium text-slate-600">Priority</th>
                <th className="text-left px-4 py-3 font-medium text-slate-600">Requester</th>
                <th className="text-left px-4 py-3 font-medium text-slate-600">Assignee</th>
              </tr>
            </thead>
            <tbody>
              {tickets.length === 0 ? (
                <tr>
                  <td colSpan={5}><EmptyState /></td>
                </tr>
              ) : (
                tickets.map((t) => (
                  <tr key={t.id} className="border-b border-slate-100 hover:bg-slate-50">
                    <td className="px-4 py-3">
                      <Link to={`/tickets/${t.id}`} className="text-blue-600 hover:underline font-medium">
                        {t.subject}
                      </Link>
                    </td>
                    <td className="px-4 py-3"><StatusPill status={t.status} /></td>
                    <td className="px-4 py-3"><PriorityPill priority={t.priority} /></td>
                    <td className="px-4 py-3 text-slate-600">{t.requester?.name}</td>
                    <td className="px-4 py-3 text-slate-600">{t.assignee?.name || '—'}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      ) : (
        <div className="flex gap-4 overflow-x-auto">
          {statusOrder.map((status) => (
            <div key={status} className="min-w-[260px] bg-slate-100 rounded-xl p-3">
              <h3 className="text-sm font-semibold text-slate-700 mb-3">
                {statusLabels[status]} ({grouped[status].length})
              </h3>
              <div className="space-y-2">
                {grouped[status].length === 0 ? (
                  <EmptyState message="No tickets" />
                ) : (
                  grouped[status].map((t) => (
                    <Link
                      key={t.id}
                      to={`/tickets/${t.id}`}
                      className="block bg-white p-3 rounded-lg border border-slate-200 hover:shadow-sm transition"
                    >
                      <p className="font-medium text-slate-800 text-sm mb-1">{t.subject}</p>
                      <div className="flex gap-2">
                        <PriorityPill priority={t.priority} />
                        <span className="text-xs text-slate-500">{t.requester?.name}</span>
                      </div>
                    </Link>
                  ))
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
