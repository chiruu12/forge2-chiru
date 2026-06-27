import { get, post, put, del } from '../../lib/api';

export function getTickets(params) {
  return get('/api/tickets', params);
}

export function getTicket(id) {
  return get(`/api/tickets/${id}`);
}

export function createTicket(data) {
  return post('/api/tickets', data);
}

export function updateTicket(id, data) {
  return put(`/api/tickets/${id}`, data);
}

export function deleteTicket(id) {
  return del(`/api/tickets/${id}`);
}
