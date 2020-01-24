import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ViewController } from 'ionic-angular';
import { SettingsProvider } from '../../../providers/providers';

@IonicPage({
  priority:'low'
})
@Component({
  selector: 'page-mini-cart',
  templateUrl: 'mini-cart.html',
})
export class MiniCartPage {
  msg: string;
  constructor(public navCtrl: NavController, public navParams: NavParams, private viewCtrl: ViewController,
    public settings: SettingsProvider) {
    this.msg = navParams.data.params;
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad MiniCartPage');
  }
  dismiss(action?) {
    this.viewCtrl.dismiss(action);
  }
}
