import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController } from 'ionic-angular';
import { SettingsProvider, WooCommerceProvider,UserProvider } from '../../../providers/providers';

@IonicPage({
  priority: 'low'
})
@Component({
  selector: 'page-reviews',
  templateUrl: 'reviews.html',
})
export class ReviewsPage {
  reviews: Array<any>;
  product: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, WC: WooCommerceProvider, private zone: NgZone,
    public settings: SettingsProvider, private user: UserProvider, private modalCtrl:ModalController) {
    this.product = navParams.data.params;
    WC.getProductReviews(this.product.id).then((data) => {
      this.zone.run(() => {
        this.reviews = data; 
      });
    });
    WC.getReviewSettings(this.product.id, this.user.id);
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad ReviewsPage');
  }

  promptReview() {
    let modal=this.modalCtrl.create('AddReviewPage',{params:this.product});
    modal.onDidDismiss((data)=>{
      if(data && data.status && data.status==='approved'){
        this.reviews.unshift(data);
      }
    })
    modal.present();
  }
}
