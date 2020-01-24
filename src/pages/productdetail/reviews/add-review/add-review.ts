import { FormBuilder, FormGroup } from '@angular/forms';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, Platform, ViewController } from 'ionic-angular';
import { UserProvider, ToastProvider, SettingsProvider, WooCommerceProvider } from '../../../../providers/providers';

@IonicPage()
@Component({
  selector: 'page-add-review',
  templateUrl: 'add-review.html',
})
export class AddReviewPage {
  product: any;
  dir: string;
  form_review: FormGroup;
  constructor(public navCtrl: NavController, public navParams: NavParams, platform: Platform, fb: FormBuilder, public user: UserProvider,
    private viewCtrl: ViewController, private toast: ToastProvider, public settings: SettingsProvider, private WC: WooCommerceProvider) {
    this.dir = platform.dir();
    this.product = navParams.data.params;
    let name = '';
    let email = '';
    if (this.user.id) {
      name = (this.user.first_name != '') ? this.user.first_name + " " + this.user.last_name : this.user.username;
      email = this.user.email;
    }
    this.form_review = fb.group({
      rating: [0],
      review: [''],
      reviewer: [name],
      reviewer_email: [email]
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad AddReviewPage');
  }
  dismiss(data?) {
    this.viewCtrl.dismiss(data);
  }
  submit() {
    let data = Object.assign({}, this.form_review.value);
    if (!data.rating || data.rating == 0) {
      this.toast.show("Rating is a required field");
      return;
    }

    if (data.review === '' || data.reviewer_email === '' || data.reviewer === '') {
      this.toast.show("Please fill requred details");
      return;
    }
    let re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!re.test(data.reviewer_email)) {
      this.toast.show("Please enter valid email");
      return;
    }
    data.product_id = this.product.id;
    if(!this.user.id){
      data.status='hold';
    }
    this.WC.postProductReviews(data).then(x => {
      console.log(x);
      if (x.status && x.status !== 'approved') {
        this.toast.show("Reviews will be published after admin approval");
      }
      this.dismiss(x);
    }, err => {
      this.toast.showError();
    });
  }
}