class BNotifications {
	constructor( serviceWorker, publicVapidKey ) {
		this.serviceWorker = serviceWorker;
		this.publicVapidKey = publicVapidKey;
	}

	async init() {
		if ( ! this.hasNotificationSupport() ) {
			return;
		}

		const hasGrantedPermission = await this.isPermissionGranted();
		if ( ! hasGrantedPermission ) {
			return;
		}

		await this.registerServiceWorker();
		await this.serviceWorkerSubscribe();
	}

	sendNotification() {
		const notification = new Notification( 'This is a Notification', {
			icon: '',
			body: '',
			data: { url: '' },
		} );

		notification.addEventListener( 'click', () => {
			window.location.href = notification.data.url;
			notification.close();
		} );
	}

	hasNotificationSupport() {
		const NotificationIsSupported = !! (
			(
				window.Notification /* W3C Specification */ ||
				window.webkitNotifications /* old WebKit Browsers */ ||
				navigator.mozNotification
			) /* Firefox for Android and Firefox OS */
		);
		return NotificationIsSupported;
	}

	isPermissionGranted() {
		return new Promise( ( resolve, reject ) => {
			if ( Notification.permission === 'granted' ) {
				resolve( true );
			} else if ( Notification.permission === 'denied' ) {
				resolve( false );
			} else {
				Notification.requestPermission().then( function ( permission ) {
					resolve( permission === 'granted' );
				} );
			}
		} );
	}

	async registerServiceWorker() {
		return new Promise( ( resolve, reject ) => {
			if ( 'serviceWorker' in navigator ) {
				navigator.serviceWorker
					.register( this.serviceWorker, {
						scope: '/',
					} )
					.then( ( registration ) => {
						resolve( true );
					} )
					.catch( ( error ) => {
						resolve( false );
					} );
			} else {
				resolve( false );
			}
		} );
	}

	async serviceWorkerSubscribe() {
		return new Promise( ( resolve, reject ) => {
			if ( 'serviceWorker' in navigator ) {
				navigator.serviceWorker.ready
					.then( ( registration ) => {
						registration.pushManager
							.subscribe( {
								userVisibleOnly: true,
								applicationServerKey: this.publicVapidKey,
							} )
							.then( ( subscription ) => {
								this.saveSubscription( subscription.toJSON() );
								resolve( true );
							} )
							.catch( ( error ) => {} );
					} )
					.catch( ( e ) => {} );
			} else {
				resolve( false );
			}
		} );
	}

	async saveSubscription( subscription ) {
		try {
			const url = '/wp-admin/admin-ajax.php?action=save_subscription';

			var formData = new FormData();

			formData.append( 'endpoint', subscription.endpoint );
			formData.append( 'p256dh', subscription.keys.p256dh );
			formData.append( 'auth', subscription.keys.auth );

			await fetch( url, {
				method: 'POST',
				body: formData,
			} );
		} catch ( e ) {}
	}
}
