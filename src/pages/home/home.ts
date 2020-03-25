import { App } from './../../app/app.config';
import { Component, ViewChild } from '@angular/core';
import { NavController, IonicPage, ModalController, Platform, Slides, Content } from 'ionic-angular';
import { WooCommerceProvider, LoadingProvider, WishlistProvider, ToastProvider, SettingsProvider } from '../../providers/providers';
import { SplashScreen } from '@ionic-native/splash-screen';
import { PageTrack } from '../../decorator/page-track.decorator';
import { AdMobFree } from '@ionic-native/admob-free';
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
	@ViewChild(Content) content: Content;
	dir: string;
	layout: any;
	storeName: string = App.store;

	constructor(public navCtrl: NavController, private WC: WooCommerceProvider, public loader: LoadingProvider,
		public wishlist: WishlistProvider, public modalCtrl: ModalController, public settings: SettingsProvider,
		toast: ToastProvider, private splash: SplashScreen, public platform: Platform, private admob: AdMobFree, public deeplinks: Deeplinks) {
		this.dir = platform.dir();
		this.WC.loadSetting();

		this.WC.getHomePageLayout().subscribe(x => {
			this.layout = x;
			this.settings.setSettings(x, 'layout');
			// if (this.platform.is('cordova')) {
			// 	this.admob.interstitial.config({
			// 		id: 'ca-app-pub-2336008794991646/2315340216',
			// 		isTesting: false,
			// 		autoShow: true,
			// 		//size: 'SMART_BANNER'
			// 	});
			// 	this.admob.interstitial.prepare()
			// 		.then(() => {
			// 			// banner Ad is ready
			// 			// if we set autoShow to false, then we will need to call the show method here
			// 		});
			// }

		}, err => {
			if (this.settings.layout) {
				this.layout = this.settings.layout;
			}
			console.log(err);
			toast.showError();
		});
		if (this.platform.is('cordova')) {
			this.deeplinks.route({}).subscribe((match) => {
				// match.$route - the route we matched, which is the matched entry from the arguments to route()
				// match.$args - the args passed in the link
				// match.$link - the full link data
				//console.log('Successfully matched route', match);
			},
				(nomatch) => {
					console.log('check deeplink');
					console.log(nomatch.$link.url);
					if (nomatch.$link.url && nomatch.$link.url.includes(App.url + '/product/') || nomatch.$link.url && nomatch.$link.url.includes(App.url + '/book/')) {
						this.loader.show();
						this.WC.getProductByUrl(nomatch.$link.url).subscribe((res) => {
							this.loader.dismiss();
							if (res) {
								this.goTo('ProductdetailPage', res);
							}
						}, err => {
							this.loader.dismiss();
						});
						console.log("go to product page");
					}
					// nomatch.$link - the full link data
					//console.error('Got a deeplink that didn\'t match', nomatch);
				});
		}

	}
	ionViewDidLoad() {
		console.log('ionViewDidLoad HomePage');
		this.splash.hide();
	}
	ionViewDidEnter() {
		if (this.slides !== undefined) {
			this.slides.resize();
			this.slides.update();
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
			this.goTo('ProductPage', this.layout.banner[index])
		}
	}
	goTo(page, params) {
		this.navCtrl.push(page, { params: params }, { animate: false });
	}

}
