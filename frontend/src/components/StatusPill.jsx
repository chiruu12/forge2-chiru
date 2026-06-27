const statusColors = {
  open: 'bg-blue-100 text-blue-700',
  in_progress: 'bg-amber-100 text-amber-700',
  pending: 'bg-purple-100 text-purple-700',
  resolved: 'bg-green-100 text-green-700',
  closed: 'bg-slate-100 text-slate-700',
};

export default function StatusPill({ status }) {
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[status] || 'bg-slate-100 text-slate-700'}`}>
      {status?.replace('_', ' ')}
    </span>
  );
}
