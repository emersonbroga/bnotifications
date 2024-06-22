self.addEventListener( 'push', ( event ) => {
	const notification = event.data.json();

	event.waitUntil(
		self.registration.showNotification( notification.title, {
			body: notification.body,
			icon: notification.icon,
			data: notification.data,
		} )
	);
} );

self.addEventListener( 'notificationclick', ( event ) => {
	event.notification.close();
	event.waitUntil( clients.openWindow( event.notification.data.url ) );
} );
