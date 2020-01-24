import { SettingsProvider, UserProvider, LoadingProvider, WooCommerceProvider, ToastProvider, RestProvider } from './../../providers/providers';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, Events, Platform } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

@IonicPage()
@Component({
  selector: 'page-guest-checkout',
  templateUrl: 'guest-checkout.html',
})
export class GuestCheckoutPage {
  formBilling: FormGroup;
  billingData: any;
  reviewData: any;
  countries: Array<any> = [];
  billingStates: Array<any> = [];
  page: string = 'billing';
  dir:string;

  constructor(public navCtrl: NavController, public navParams: NavParams, private formBuilder: FormBuilder,
    public loader: LoadingProvider, private user: UserProvider, public WC: WooCommerceProvider,platform:Platform,
    private rest: RestProvider, public zone: NgZone, private events: Events, private translate: TranslateService,
    public settings: SettingsProvider, private toast: ToastProvider) {
      this.dir=platform.dir();
      if(this.user.billing){
        this.billingData = this.user.billing;
        this.formBilling = this.formBuilder.group({
          first_name: [this.billingData.first_name, Validators.required],
          last_name: [this.billingData.last_name, Validators.required],
          company: [this.billingData.company],
          email: [this.billingData.email],
          phone: [this.billingData.phone, Validators.required],
          city: [this.billingData.city],
          state: [this.billingData.state],
          postcode: [this.billingData.postcode],
          address_1: [this.billingData.address_1, Validators.required],
          address_2: [this.billingData.address_2],
          country: [this.billingData.country] 
        });
      }else{
        this.formBilling = this.formBuilder.group({
          first_name: ['', Validators.required],
          last_name: ['', Validators.required],
          company: [''],
          email: [''],
          phone: ['', Validators.required],
          city: [''],
          state: [''],
          postcode: [''],
          address_1: ['', Validators.required],
          address_2: [''],
          country: [''] 
        });
      }

    
    //this.countries = this.settings.countryList;
    //this.getBillingStates();
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad CheckoutPage');
  }

  getReview(method?, payment_method?) {
    this.reviewData = undefined;
    this.rest.getReview(method, payment_method).then((res: any) => {
      console.log(res.data);
      let data = JSON.parse(res.data);
      let payment = [];
      for (let i = 0; i < data.payment_gateway.length; i++) {
        if (data.payment_gateway[i].gateway_id == 'cod' ||
          data.payment_gateway[i].gateway_id == 'paytm' ||
          data.payment_gateway[i].gateway_id == 'bacs' ||
          data.payment_gateway[i].gateway_id == 'cheque' ||
          data.payment_gateway[i].gateway_id == 'wallet' ||
          data.payment_gateway[i].gateway_id == 'razorpay') {
          payment.push(data.payment_gateway[i]);
        }
      }
      data.payment_gateway = payment;
      this.zone.run(() => {
        this.reviewData = data;
        console.log(this.reviewData);
      });
    }).catch((err) => {
      console.log(err);
    });
  }

  getBillingStates() {
    let id = this.formBilling.value.country || this.billingData.state;
    this.billingStates = this.settings.getState(id);
    console.log(this.billingStates);
    if (this.billingStates.length == 0) {
      this.formBilling.controls['state'].setValue('');
    }
  }

  billingSubmit() {
    if (!this.formBilling.valid || (this.billingStates.length > 0 && this.formBilling.get('state').value == '')) {
      this.translate.get(['FILL_REQUIRED_FIELD']).subscribe((x) => {
        this.toast.show(x.FILL_REQUIRED_FIELD);
      });
      return;
    }
    console.log("billing");
    this.loader.show();
    if (this.settings.all.appSettings.pincode_active) {
      let postcode =  this.formBilling.value.postcode ;
      console.log(postcode);
      this.WC.checkPincode(postcode).subscribe((res: any) => {
        console.log(res);
        if (res.delivery) {
          this.finalSubmit();
        } else {
          this.loader.dismiss();
          this.translate.get(['DELIVERY_NOT_AVAILABLE']).subscribe(x => {
            this.toast.show(x.DELIVERY_NOT_AVAILABLE);
          });
        }
      }, (err) => {
        console.log(err);
        this.loader.dismiss();
        this.toast.showError();
      });
  } else {
      this.finalSubmit();
    }
  }
  finalSubmit() {
    let data: any = {};
    data.billing = this.formBilling.value;
    this.billingData=this.formBilling.value;
    if(this.user.id){
    this.WC.updateUserInfo(this.user.id, data).then((res) => {
      console.log(res);
      this.loader.dismiss();
      this.user.loggedIn(res);
      this.goToReview();
    }, (err) => {
      console.error(err);
    });
  }else{
    this.loader.dismiss();

    this.goToReview();
  }
  }

  goToBilling() {
    this.page = "billing";
  }
  goToReview() {
    this.page = "review";
    this.getReview();
  }
  goBack() {
   this.page =  "billing" ;
  }

  orderNow() {
    if (!this.reviewData.chosen_gateway || this.reviewData.chosen_gateway === '') {
      this.translate.get(['SELECT_PAYMENT_GATEWAY']).subscribe(x => {
        this.toast.show(x.SELECT_PAYMENT_GATEWAY);
      });
    }
    let data: any = {
      pincode_meta:{},
      billing:this.billingData
    };
    if (this.settings.all.appSettings.pincode_active) {
      for (let item of this.reviewData.product) {
        if (item.delivery)
          data.pincode_meta[item.product_id] = item.delivery_date
      }
    }
    let payment_method = this.reviewData.chosen_gateway;
    let shipping_method;
    if (this.reviewData.chosen_shipping_method) {
      shipping_method = this.reviewData.chosen_shipping_method;
    }
    console.log(data);
    this.loader.show();
    this.rest.createOrder(payment_method, shipping_method, data).then((res: any) => {
      let data = JSON.parse(res.data);
      console.log(res);
      this.rest.getCartClear();
      this.events.publish("cartchanged");
      this.navCtrl.push('PaymentPage', { params: data });
      this.loader.dismiss();
    }, (err) => {
      this.loader.dismiss();
      console.log(err);
    });
  }
  shipping_price() {
    return this.reviewData.shipping_method.find(x => x.id == this.reviewData.chosen_shipping_method).shipping_method_price;
  }
}
