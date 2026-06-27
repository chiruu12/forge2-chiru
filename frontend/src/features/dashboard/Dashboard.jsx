import { useEffect, useState } from 'react';
import { getMetrics } from './api';
import Spinner from '../../components/Spinner';

export default function Dashboard() {
  const [metrics, setMetrics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    async function load() {
      try {
        const res = await getMetrics();
        setMetrics(res.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, []);

  if (loading) return <Spinner />;
  if (error) return <div className="p-6 text-red-600">{error}</div>;
  if (!metrics) return null;

  const cards = [
    { label: 'Open', value: metrics.open_count, color: 'bg-blue-50 border-blue-200 text-blue-700' },
    { label: 'Pending', value: metrics.pending_count, color: 'bg-purple-50 border-purple-200 text-purple-700' },
    { label: 'Resolved', value: metrics.resolved_count, color: 'bg-green-50 border-green-200 text-green-700' },
    { label: 'Closed', value: metrics.closed_count, color: 'bg-slate-50 border-slate-200 text-slate-700' },
  ];

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold text-slate-800 mb-6">Dashboard</h1>
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {cards.map((c) => (
          <div key={c.label} className={`p-4 rounded-xl border ${c.color}`}>
            <p className="text-sm font-medium opacity-80">{c.label}</p>
            <p className="text-3xl font-bold mt-1">{c.value}</p>
          </div>
        ))}
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="p-4 rounded-xl border border-red-200 bg-red-50 text-red-700">
          <p className="text-sm font-medium opacity-80">Urgent Open</p>
          <p className="text-3xl font-bold mt-1">{metrics.urgent_open_count}</p>
        </div>
        <div className="p-4 rounded-xl border border-slate-200 bg-slate-50 text-slate-700">
          <p className="text-sm font-medium opacity-80">Total Tickets</p>
          <p className="text-3xl font-bold mt-1">{metrics.total_tickets}</p>
        </div>
      </div>
    </div>
  );
}
