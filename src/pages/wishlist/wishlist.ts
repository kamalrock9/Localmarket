import { TranslateService } from '@ngx-translate/core';
import { WishlistProvider, SettingsProvider } from './../../providers/providers';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, AlertController, ModalController } from 'ionic-angular';


@IonicPage({
  priority:'off'
})
@Component({
  selector: 'page-wishlist',
  templateUrl: 'wishlist.html',
})
export class WishlistPage {
  products: any = [];
  constructor(public navCtrl: NavController, public navParams: NavParams, public wishlist: WishlistProvider,private modalCtrl:ModalController,
    public alertCtrl: AlertController, public translate: TranslateService, public settings: SettingsProvider) {
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad WishlistPage');
  }
  ionViewWillEnter() {
    this.wishlist.load().then(() => {
      this.products = this.wishlist.all;
    });
  }
  goTo(page, params) {
    this.navCtrl.push(page, { params: params });
  }
  goHome() {
    this.navCtrl.parent.select(0);
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
  remove(product) {
    this.translate.get(['REMOVE_FROM_WISHLIST', 'ARE_YOU_SURE', 'NO', 'YES']).subscribe((x) => {
      let confirm = this.alertCtrl.create({
        title: x.REMOVE_FROM_WISHLIST,
        message: x.ARE_YOU_SURE,
        buttons: [{
          text: x.NO
        }, {
          text: x.YES,
          handler: () => {
            this.reloadWish(product);
          }
        }]
      });
      confirm.present();
    });
  }
  reloadWish(product) {
    this.wishlist.remove(product);
    this.products = this.wishlist.all;
  }

}
