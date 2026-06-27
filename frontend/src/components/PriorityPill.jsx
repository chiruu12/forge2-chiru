const priorityColors = {
  low: 'bg-slate-100 text-slate-600',
  medium: 'bg-blue-50 text-blue-600',
  high: 'bg-orange-50 text-orange-600',
  urgent: 'bg-red-50 text-red-600',
};

export default function PriorityPill({ priority }) {
  return (
    <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${priorityColors[priority] || 'bg-slate-100 text-slate-600'}`}>
      {priority}
    </span>
  );
}
