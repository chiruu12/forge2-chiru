import { get } from '../../lib/api';

export function getMetrics() {
  return get('/api/dashboard/metrics');
}
