import { SettingsProvider, UserProvider, LoadingProvider, WooCommerceProvider, ToastProvider, RestProvider } from './../../providers/providers';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Component, NgZone, ViewChild } from '@angular/core';
import { IonicPage, NavController, NavParams, Events, Content } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';


@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-checkout',
  templateUrl: 'checkout.html',
})
export class CheckoutPage {
  @ViewChild(Content) content: Content;
  formBilling: FormGroup;
  isToggled: boolean = true;
  formShipping: FormGroup;
  billingData: any;
  reviewData: any;
  countries: Array<any> = [];
  billingStates: Array<any> = [];
  shippingData: any;
  shippingStates: Array<any> = [];
  page: string = 'billing';
  objectKeys = Object.keys;


  constructor(public navCtrl: NavController, public navParams: NavParams, private formBuilder: FormBuilder,
    public loader: LoadingProvider, private user: UserProvider, public WC: WooCommerceProvider,
    private rest: RestProvider, public zone: NgZone, private events: Events, private translate: TranslateService,
    public settings: SettingsProvider, private toast: ToastProvider) {

    this.billingData = this.user.billing;
    this.shippingData = this.user.shipping;

    this.formBilling = this.formBuilder.group({
      first_name: [this.billingData.first_name || this.user.first_name, Validators.required],
      last_name: [this.billingData.last_name || this.user.last_name],
      company: [this.billingData.company],
      email: [this.billingData.email || this.user.email, Validators.required],
      phone: [this.billingData.phone, Validators.required],
      city: [this.billingData.city, Validators.required],
      state: [this.billingData.state],
      postcode: [this.billingData.postcode || settings.postcode, Validators.required],
      address_1: [this.billingData.address_1, Validators.required],
      address_2: [this.billingData.address_2],
      country: [this.billingData.country || 'IN', Validators.required]
    });

    this.formShipping = this.formBuilder.group({
      first_name: [this.shippingData.first_name || this.user.first_name, Validators.required],
      last_name: [this.shippingData.last_name || this.user.last_name],
      company: [this.shippingData.company],
      city: [this.shippingData.city, Validators.required],
      state: [this.shippingData.state],
      postcode: [this.shippingData.postcode || settings.postcode, Validators.required],
      address_1: [this.shippingData.address_1, Validators.required],
      address_2: [this.shippingData.address_2],
      country: [this.shippingData.country || 'IN', Validators.required]
    });

    this.countries = this.settings.countryList;
    this.getBillingStates();
    this.getShippingStates();

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad CheckoutPage');
  }

  getReview(method?, payment_method?, pay_via_wallet?) {
    console.log(pay_via_wallet);
    this.reviewData = undefined;
    this.rest.getReview(method, payment_method, pay_via_wallet).then((res: any) => {
      console.log(res);
      let data = JSON.parse(res.data);
      if (data.err) {
        this.toast.show(data.err);
        this.goBack();
      } else {
        let payment = [];
        if (data.payment_gateway && data.payment_gateway.length > 0) {
          for (let i = 0; i < data.payment_gateway.length; i++) {
            if (data.payment_gateway[i].gateway_id == 'cod' ||
              data.payment_gateway[i].gateway_id == 'paytm' ||
              data.payment_gateway[i].gateway_id == 'bacs' ||
              data.payment_gateway[i].gateway_id == 'cheque' ||
              data.payment_gateway[i].gateway_id == 'razorpay' ||
              data.payment_gateway[i].gateway_id == 'instamojo' ||
              data.payment_gateway[i].gateway_id == 'pumcp' ||
              data.payment_gateway[i].gateway_id == 'wallet') {
              payment.push(data.payment_gateway[i]);
            }
          }
        }
        data.payment_gateway = payment;
        this.zone.run(() => {
          this.reviewData = data;
          console.log(this.reviewData);
          this.content.resize();

        });
      }
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

  getShippingStates() {
    let id = this.formShipping.value.country || this.shippingData.state;
    this.shippingStates = this.settings.getState(id);
    if (this.shippingStates.length == 0) {
      this.formShipping.controls['state'].setValue('');
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
    let data: any = {};
    data.billing = this.formBilling.value;
    if (this.isToggled) {
      this.shippingSubmit();
    } else {
      this.goToShipping();
    }
  }

  shippingSubmit(isValidate?) {
    if (isValidate && (!this.formShipping.valid || (this.shippingStates.length > 0 && this.formShipping.get('state').value == ''))) {
      this.translate.get(['FILL_REQUIRED_FIELD']).subscribe((x) => {
        this.toast.show(x.FILL_REQUIRED_FIELD);
      });
      return;
    }
    this.loader.show();
    if (this.settings.all.appSettings.pincode_active) {
      let postcode = this.isToggled ? this.formBilling.value.postcode : this.formShipping.value.postcode;
      console.log(postcode);
      this.WC.checkPincode(postcode).subscribe((res: any) => {
        console.log(res);
        if (res.delivery) {
          this.finalShippingSubmit();
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
      this.finalShippingSubmit();
    }
  }
  finalShippingSubmit() {
    let data: any = {};
    data.billing = this.formBilling.value;
    if (this.isToggled) {
      data.shipping = this.formBilling.value;
    } else {
      data.shipping = this.formShipping.value;
    }
    this.WC.updateUserInfo(this.user.id, data).then((res) => {
      console.log(res);
      this.loader.dismiss();
      this.user.loggedIn(res);
      this.goToReview();
    }, (err) => {
      this.loader.dismiss();
      console.error(err);
    });
  }

  goToBilling() {
    this.page = "billing";
    this.content.scrollToTop(0);
    this.content.resize();
  }
  goToShipping() {
    this.page = "shipping";
    this.content.scrollToTop(0);
    this.content.resize();
  }
  goToReview() {
    this.page = "review";
    this.content.scrollToTop(0);
    this.content.resize();
    this.getReview();
  }
  goBack() {
    console.log(this.isToggled);
    if (this.page == 'review') {
      this.page = this.isToggled ? "billing" : "shipping";
    } else {
      this.page = "billing";
    }
    this.content.resize();
  }
  // notify() {
  //   this.isToggled = !this.isToggled;
  //   console.log(this.isToggled);
  // }

  orderNow() {
    if (!this.reviewData.chosen_gateway || this.reviewData.chosen_gateway === '') {
      this.translate.get(['SELECT_PAYMENT_METHOD']).subscribe(x => {
        this.toast.show(x.SELECT_PAYMENT_METHOD);
      });
      return;
    }
    let data: any = {};
    if (this.settings.all.appSettings.pincode_active) {
      for (let item of this.reviewData.product) {
        if (item.delivery)
          data[item.product_id] = item.delivery_date
      }
    }
    let payment_method = this.reviewData.chosen_gateway;
    let shipping_method;
    if (this.reviewData.shipping_method && this.reviewData.shipping_method.length > 0 && !this.reviewData.chosen_shipping_method) {
      this.toast.show("Select a shipping method");
      return;
    }
    if (this.reviewData.chosen_shipping_method) {
      shipping_method = this.reviewData.chosen_shipping_method;
    }
    //console.log(payment_method);
    this.loader.show();
    this.rest.createOrder(payment_method, shipping_method, data, this.reviewData.pay_via_wallet).then((res: any) => {
      console.log(res);
      let data = JSON.parse(res.data);
      this.rest.getCartClear().then(() => {
        this.events.publish("cartchanged");
      });
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
