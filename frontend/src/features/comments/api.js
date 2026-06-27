import { post } from '../../lib/api';

export function createComment(ticketId, data) {
  return post(`/api/tickets/${ticketId}/comments`, data);
}
