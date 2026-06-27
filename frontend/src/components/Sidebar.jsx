import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../lib/auth.jsx';

export default function Sidebar() {
  const { user, logout } = useAuth();
  const location = useLocation();

  const nav = [
    { to: '/dashboard', label: 'Dashboard', icon: '📊' },
    { to: '/tickets', label: 'Tickets', icon: '🎫' },
  ];

  const isActive = (path) => location.pathname === path || location.pathname.startsWith(path + '/');

  return (
    <aside className="w-64 bg-slate-900 text-white flex flex-col h-screen">
      <div className="p-6 flex items-center gap-3">
        <div className="w-8 h-8 bg-gradient-to-br from-slate-600 to-blue-500 rounded-lg flex items-center justify-center font-bold text-lg">
          ◑
        </div>
        <span className="font-bold text-lg">PulseDesk</span>
      </div>

      <nav className="flex-1 px-4 space-y-1">
        {nav.map((item) => (
          <Link
            key={item.to}
            to={item.to}
            className={`flex items-center gap-3 px-3 py-2 rounded-lg transition ${
              isActive(item.to) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800'
            }`}
          >
            <span>{item.icon}</span>
            <span>{item.label}</span>
          </Link>
        ))}
      </nav>

      <div className="p-4 border-t border-slate-800">
        <div className="mb-3 px-3">
          <p className="text-sm font-medium text-white">{user?.name}</p>
          <p className="text-xs text-slate-400 capitalize">{user?.role}</p>
        </div>
        <button
          onClick={logout}
          className="w-full text-left px-3 py-2 text-slate-300 hover:bg-slate-800 rounded-lg transition"
        >
          🚪 Sign out
        </button>
      </div>
    </aside>
  );
}
