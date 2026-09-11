@if(auth()->check() && \App\Support\FirebaseWebPush::isConfigured())
@php
    $firebaseConfig = \App\Support\FirebaseWebPush::firebaseJsConfig();
    $vapidKey = \App\Support\FirebaseWebPush::vapidKey();
    $swUrl = url('/firebase-messaging-sw.js');
    $saveUrl = url('/web/fcm-token');
@endphp
<script src="https://www.gstatic.com/firebasejs/11.0.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging-compat.js"></script>
<script>
(function () {
  const firebaseConfig = @json($firebaseConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  const vapidKey = @json($vapidKey, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  const swUrl = @json($swUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  const saveUrl = @json($saveUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  const storageKey = 'adan_fcm_web_registered';

  if (!('serviceWorker' in navigator) || !('Notification' in window)) {
    return;
  }

  if (sessionStorage.getItem(storageKey) === '1') {
    return;
  }

  function csrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  firebase.initializeApp(firebaseConfig);

  navigator.serviceWorker.register(swUrl).then(function (reg) {
    firebase.messaging().useServiceWorker(reg);
    return Notification.requestPermission();
  }).then(function (perm) {
    if (perm !== 'granted') {
      return null;
    }
    return firebase.messaging().getToken({ vapidKey: vapidKey });
  }).then(function (token) {
    if (!token) {
      return;
    }
    return fetch(saveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ fcm_token: token }),
    });
  }).then(function (res) {
    if (res && res.ok) {
      sessionStorage.setItem(storageKey, '1');
    }
  }).catch(function () {});
})();
</script>
@endif
