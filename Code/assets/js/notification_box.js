document.addEventListener("DOMContentLoaded", function () {

  function changePushButtonState(test = '') { }

  const applicationServerKey =
    'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0';

  navigator.serviceWorker.register('serviceWorker.js').then(
    () => {
      updateSubscription();
    },
    (e) => {
      changePushButtonState('incompatible');
    }
  );

  function checkNotificationPermission(isTheBoxChecked = true) {

    if (Notification.permission === 'default' && isTheBoxChecked) {
      const notificationBox = document.getElementById("notificationBox");
      const allowButton = document.getElementById("allowButton");
      const denyButton = document.getElementById("denyButton");
      notificationBox.style.display = "block";

      allowButton.addEventListener("click", function () {
        notificationBox.style.display = "none";
        console.log("allow btn clicked");
        push_subscribe();
      });

      denyButton.addEventListener("click", function () {
        console.log("Notification permission denied");
        notificationBox.style.display = "none";
      });
    } else if (Notification.permission === 'denied') {
      notificationBox.style.display = "none";
      console.log('Push messages are blocked.');
    } else if (Notification.permission === 'granted') {
      notificationBox.style.display = "none";
    }

  }

  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  function askForNotificationPermission() {
    return new Promise((resolve, reject) => {
      if (Notification.permission === 'denied') {
        return reject(new Error('Push messages are blocked.'));
      }

      if (Notification.permission === 'granted') {
        return resolve();
      }

      if (Notification.permission === 'default') {
        return Notification.requestPermission().then(result => {
          if (result !== 'granted') {
            reject(new Error('Bad permission result'));
          } else {
            resolve();
          }
        });
      }
      return reject(new Error('Unknown permission'));
    });
  }

  function push_subscribe() {
    return askForNotificationPermission()
      .then(() => navigator.serviceWorker.ready)
      .then(serviceWorkerRegistration =>
        serviceWorkerRegistration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
        })
      )
      .then(subscription => {
        return push_sendSubscriptionToServer(subscription, 'POST');
      })
      .then((subscription) => subscription && changePushButtonState('enabled'))
      .catch((e) => {
        if (Notification.permission === 'denied') {
          console.warn('Notifications are denied by the user.');
          changePushButtonState('incompatible');
        } else {
          console.error('Impossible to subscribe to push notifications', e);
          changePushButtonState('disabled');
        }
      });
  }

  function push_sendSubscriptionToServer(subscription, method) {
    // if (new Date().toISOString().split('T')[0] !== localStorage.getItem("notificationSubSyncedAt")) {
    if (true) {
      navigator.serviceWorker.ready
        .then((serviceWorkerRegistration) => serviceWorkerRegistration.pushManager.getSubscription())
        .then((subscription) => {
          if (!subscription) {
            alert('Please enable push notifications');
            return;
          }
          const jsonSubscription = subscription.toJSON();
          return fetch('api/push_subscription', {
            method,
            body: JSON.stringify(jsonSubscription)
          })
            .then((response) => {
              if (response.ok) {
                // localStorage.setItem('notificationSubSyncedAt', new Date().toISOString().split('T')[0]);
                return response.json()
              }
            })
            .then((data) => {
              console.log(data);
            });
        });
    }
  }

  function updateSubscription() {
    navigator.serviceWorker.ready
      .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
      .then(subscription => {
        changePushButtonState('disabled');
        if (!subscription) {
          return;
        }
        return push_sendSubscriptionToServer(subscription, 'POST');
      })
      .then(subscription => subscription && changePushButtonState('enabled')) // Set your UI to show they have subscribed for push messages
      .catch(e => {
        console.error('Error when updating the subscription', e);
      });
  }
  checkNotificationPermission();
});