import { AdMobFree } from '@ionic-native/admob-free';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, Platform } from 'ionic-angular';
import { WooCommerceProvider } from '../../providers/providers';


@IonicPage({
  priority: 'low'
})
@Component({
  selector: 'page-tos', 
  templateUrl: 'tos.html',
})
export class TosPage {
  tos: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, private WC: WooCommerceProvider,
    private platform:Platform,private admob:AdMobFree) {
    this.WC.getTermConditon().subscribe((x: any) => {
      console.log(x);
      if (x.term_condition && x.term_condition != '') {
        this.tos = x.term_condition;
      } else {
        this.tos = "Please Set a terms in backend panel";
      }
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad TosPage');
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

}
