import { WooCommerceProvider } from './../../providers/woocommerce/woocommerce';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';

@IonicPage({
  priority:'off'
})
@Component({
  selector: 'page-faq',
  templateUrl: 'faq.html',
})
export class FaqPage {
  toggles: any = {}
  Object = Object;
  faq: Array<any>;
  constructor(public navCtrl: NavController, public navParams: NavParams, public WC: WooCommerceProvider,
    zone:NgZone) {
    this.WC.getFAQ().subscribe((res:any) => {
      console.log(res);
      zone.run(()=>{
        this.faq=res;
        for(let i in this.faq){
          this.toggle[i]=false;
        }
      });
    }, err => {

    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad AccountHelpPage');
  }
  toggle(val) {
    this.toggles[val] = !this.toggles[val];
  }

}
