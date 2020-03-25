import { AdMobFree } from '@ionic-native/admob-free';
import { UserProvider, SettingsProvider } from './../../providers/providers';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController, Platform } from 'ionic-angular';

@IonicPage({
  priority: 'off'
})
@Component({
  selector: 'page-address',
  templateUrl: 'address.html',
})
export class AddressPage {

  constructor(public navCtrl: NavController, public navParams: NavParams, public modalCtrl: ModalController,
    public user: UserProvider, public settings: SettingsProvider, private platform: Platform, private admob: AdMobFree) {

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad AddressPage');
  }
  ionViewDidEnter() {
    if (this.platform.is('cordova')) {
      this.admob.banner.config({
        id: 'ca-app-pub-2336008794991646/9868442895',
        isTesting: false,
        autoShow: true,
        size: 'SMART_BANNER'
      });
      this.admob.banner.prepare();
    }
  }
  ionViewDidLeave() {
    console.log('ionViewDidLoad: ProductdetailPage');
    if (this.platform.is('cordova')) {
      this.admob.banner.remove();
    }
  }
  createModal(page, params) {
    let modal = this.modalCtrl.create(page, { params: params });
    modal.onDidDismiss((data) => {

    });
    modal.present(); 
  }
  isEmptyObject(o) {
    return Object.keys(o).every(function (x) {
      return o[x] === '' || o[x] === null;  // or just "return o[x];" for falsy values
    });
  }

}
