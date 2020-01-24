import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
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
  constructor(public navCtrl: NavController, public navParams: NavParams, private WC: WooCommerceProvider) {
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

}
