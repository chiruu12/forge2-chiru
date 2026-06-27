import { createBrowserRouter, Navigate } from 'react-router-dom';
import Login from './features/auth/Login';
import Register from './features/auth/Register';
import Dashboard from './features/dashboard/Dashboard';
import TicketBoard from './features/tickets/TicketBoard';
import TicketDetail from './features/tickets/TicketDetail';
import ProtectedRoute from './components/ProtectedRoute';
import Sidebar from './components/Sidebar';

function Layout({ children }) {
  return (
    <div className="flex h-screen bg-slate-50">
      <Sidebar />
      <main className="flex-1 overflow-auto">{children}</main>
    </div>
  );
}

const router = createBrowserRouter([
  { path: '/login', element: <Login /> },
  { path: '/register', element: <Register /> },
  {
    path: '/',
    element: <Navigate to="/dashboard" replace />,
  },
  {
    path: '/dashboard',
    element: (
      <ProtectedRoute>
        <Layout><Dashboard /></Layout>
      </ProtectedRoute>
    ),
  },
  {
    path: '/tickets',
    element: (
      <ProtectedRoute>
        <Layout><TicketBoard /></Layout>
      </ProtectedRoute>
    ),
  },
  {
    path: '/tickets/:id',
    element: (
      <ProtectedRoute>
        <Layout><TicketDetail /></Layout>
      </ProtectedRoute>
    ),
  },
]);

export default router;
