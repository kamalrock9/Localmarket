import { UserProvider,SettingsProvider } from './../../providers/providers';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController } from 'ionic-angular';

@IonicPage({
  priority:'off'
})
@Component({
  selector: 'page-address',
  templateUrl: 'address.html',
})
export class AddressPage {

  constructor(public navCtrl: NavController, public navParams: NavParams, public modalCtrl: ModalController,
    public user: UserProvider, public settings: SettingsProvider) {

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad AddressPage');
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
