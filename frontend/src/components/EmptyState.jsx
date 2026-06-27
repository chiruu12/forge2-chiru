export default function EmptyState({ message = "Nothing here yet." }) {
  return (
    <div className="flex flex-col items-center justify-center p-12 text-slate-400">
      <div className="text-4xl mb-2">📭</div>
      <p className="text-sm">{message}</p>
    </div>
  );
}
