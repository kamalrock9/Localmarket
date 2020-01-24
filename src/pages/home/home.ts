import { NotifProvider } from './../../providers/notif/notif';
import { OneSignal } from '@ionic-native/onesignal';
import { App } from './../../app/app.config';
import { Component, ViewChild } from '@angular/core';
import { NavController, IonicPage, ModalController, Platform, Slides, Content } from 'ionic-angular';
import { WooCommerceProvider, LoadingProvider, WishlistProvider, ToastProvider, SettingsProvider } from '../../providers/providers';
import { SplashScreen } from '@ionic-native/splash-screen';
import { PageTrack } from '../../decorator/page-track.decorator';
import { Deeplinks } from '@ionic-native/deeplinks';


@IonicPage({
	priority: 'high'
})
@PageTrack({ pageName: 'Home Page' })
@Component({
	selector: 'page-home',
	templateUrl: 'home.html'
})
export class HomePage {
	@ViewChild('slider') slides: Slides;
	@ViewChild(Slides) slidesAll: Slides;
	@ViewChild(Content) content: Content;
	dir: string;
	layout: any;
	storeName: string = App.store;

	constructor(public navCtrl: NavController, private WC: WooCommerceProvider, public loader: LoadingProvider,
		public wishlist: WishlistProvider, public modalCtrl: ModalController, public settings: SettingsProvider,
		toast: ToastProvider, private splash: SplashScreen, public platform: Platform, public deeplinks: Deeplinks,
		private oneSignal: OneSignal, private notif: NotifProvider) {
		this.dir = platform.dir();
		this.WC.loadSetting();

		this.WC.getHomePageLayout().subscribe(x => {
			this.layout = x;
			this.settings.setSettings(x, 'layout');
		}, err => {
			if (this.settings.layout) {
				this.layout = this.settings.layout;
			}
			console.log(err);
			toast.showError();
		});
		if (this.platform.is('cordova')) {
			if (this.settings.all.appSettings.one_signal_app_id &&
				this.settings.all.appSettings.one_signal_app_id != '' &&
				this.settings.all.appSettings.google_project_number &&
				this.settings.all.appSettings.google_project_number != '') {

				this.oneSignal.startInit(this.settings.all.appSettings.one_signal_app_id, this.settings.all.appSettings.google_project_number);
				this.oneSignal.inFocusDisplaying(this.oneSignal.OSInFocusDisplayOption.Notification);
				this.oneSignal.handleNotificationReceived().subscribe((x) => {
					// do something when notification is received
					console.log(x);
					if (x && x.payload) {
						this.notif.post(x.payload);
					}
				});
				this.oneSignal.handleNotificationOpened().subscribe((x) => {
					// do something when a notification is opened
					console.log(x);
					if (x && x.notification && x.notification.payload) {
						this.notif.post(x.notification.payload);
						if (x.notification.payload.additionalData && x.notification.payload.additionalData.product_id) {
							let params = {
								id: x.notification.payload.additionalData.product_id,
								isReferedByPush: true
							}
							this.goTo('ProductdetailPage', params);

						} else if (x.notification.payload.additionalData && x.notification.payload.additionalData.category_id) {
							let params = {
								id: x.notification.payload.additionalData.category_id
							}
							this.goTo('ProductPage', params);
						} else if (x.notification.payload.additionalData && x.notification.payload.additionalData.brand_id) {
							let params = {
								brand_id: x.notification.payload.additionalData.brand_id
							}
							this.goTo('ProductPage', params);
						}
					}
				});
				this.oneSignal.endInit();
			}
			this.deeplinks.route({}).subscribe((match) => {
				// match.$route - the route we matched, which is the matched entry from the arguments to route()
				// match.$args - the args passed in the link
				// match.$link - the full link data
				//console.log('Successfully matched route', match);
			},
				(nomatch) => {
					console.log(nomatch.$link.url);
					if (nomatch.$link.url && nomatch.$link.url.includes(App.url + '/product/')) {
						let params = {
							link: nomatch.$link.url,
							isReferedByDeeplinks: true
						}
						this.goTo('ProductdetailPage', params);
						console.log("go to product details page");
					}
				});
		}
	}
	ionViewDidLoad() {
		console.log('ionViewDidLoad HomePage');
		this.splash.hide();
	}
	ionViewDidEnter() {
		if (this.slides !== undefined) {
			this.slidesAll.resize();
			this.slidesAll.update();
			this.slides.autoplayDisableOnInteraction = false;
			this.slides.startAutoplay();
		}
	}
	ionViewWillLeave() {
		if (this.slides !== undefined) {
			this.slides.stopAutoplay();
		}
	}

	setFav(product: any) {
		this.wishlist.add(product);
	}

	showSearch() {
		let modal = this.modalCtrl.create('SearchPage', {});
		modal.onDidDismiss((data) => {
			if (data && data.page) {
				this.goTo(data.page, data.params);
			}
		});
		modal.present();
	}
	handleSlideClick() {
		//Using This Function due to there is a bug in ion-slides in autoplay loop can't detect 1st silde click
		//Need To fix when official team fix this
		let index = this.slides._slides[this.slides.clickedIndex].getAttribute('data-swiper-slide-index');
		console.log(index);
		if (index) {
			this.gridClick('ProductPage', this.layout.banner[index])
		}
	}
	goTo(page, params) {
		this.navCtrl.push(page, { params: params }, { animate: false });
	}
	gridClick(page, data) {
		let params: any = {};
		if (data && data.type == 'brand') {
			params.brand_id = data.id;
			params.brand_name = data.name;
		} else {
			params = data;
		}
		this.goTo(page, params)
	}

}
