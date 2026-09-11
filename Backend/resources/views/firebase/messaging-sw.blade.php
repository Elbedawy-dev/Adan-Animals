importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-messaging-compat.js');
firebase.initializeApp(@json($firebaseConfig, JSON_UNESCAPED_SLASHES));
const messaging = firebase.messaging();
messaging.onBackgroundMessage(async function (payload) {
  const title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'Notification';
  const body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || (payload.data && payload.data.message) || '';
  const windowClients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
  windowClients.forEach(function (client) {
    client.postMessage({ type: 'adan:fcm-message', payload: payload });
  });
  const hasVisibleClient = windowClients.some(function (client) {
    return client.visibilityState === 'visible';
  });
  if (hasVisibleClient) {
    return;
  }
  return self.registration.showNotification(title, {
    body: body,
    icon: '/icon-192.svg',
    badge: '/icon-192.svg',
    data: payload.data || {},
  });
});
