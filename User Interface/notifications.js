// Function to request permission for notifications
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
            } else {
                console.log('Notification permission denied.');
            }
        });
    } else {
        console.log('This browser does not support notifications.');
    }
}

// Function to show a notification
function showNotification() {
    if (Notification.permission === 'granted') {
        const options = {
            body: 'Notifications are enabled for this app!',
            icon: 'https://picsum.photos/100/100', // You can replace with your app's icon
        };
        const notification = new Notification('Notification Title', options);
        
        notification.onclick = function(event) {
            window.focus(); // Bring the tab to the foreground when clicked
        };
    }
}

// Add an event listener to the toggle button
document.getElementById('notificationToggle').addEventListener('change', function() {
    if (this.checked) {
        requestNotificationPermission();  // Request permission when the toggle is turned on
        showNotification();               // Show a notification
    } else {
        console.log('Notifications Disabled');
        // You can add logic to disable or stop notifications if needed
    }
});

const options = {
    body: 'You have new updates!',
    icon: 'https://example.com/icon.png',
    requireInteraction: true, // Notification stays until interacted with
};
const notification = new Notification('App Alert!', options);
