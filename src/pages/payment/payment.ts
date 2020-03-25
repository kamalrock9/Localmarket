import { WooCommerceProvider } from './../../providers/woocommerce/woocommerce';
import { LoadingProvider } from './../../providers/loading/loading';
import { App } from './../../app/app.config';
import { Component, ViewChild } from '@angular/core';
import { IonicPage, NavController, NavParams, Navbar, Platform, IonicApp } from 'ionic-angular';
import { InAppBrowser } from '@ionic-native/in-app-browser';
import { SettingsProvider, ToastProvider } from '../../providers/providers';
import { TranslateService } from '@ngx-translate/core';
declare var RazorpayCheckout: any;
declare var paytm: any;

@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-payment',
  templateUrl: 'payment.html',
})
export class PaymentPage {
  @ViewChild(Navbar) navBar: Navbar;
  orderDetails: any;
  isShoppingComplete: boolean = false;
  unregisterBackButtonAction: any;

  constructor(public navCtrl: NavController, public navParams: NavParams, private iab: InAppBrowser, private WC: WooCommerceProvider,
    public settings: SettingsProvider, private platform: Platform, private loader: LoadingProvider, private toast: ToastProvider,
    private translate: TranslateService, private ionicApp: IonicApp) {
    this.orderDetails = navParams.data.params;
    console.log(this.orderDetails);
    if (this.orderDetails.payment_method == 'cod' ||
      this.orderDetails.payment_method == 'bacs' ||
      this.orderDetails.payment_method == 'cheque') {
      this.isShoppingComplete = true;
    } else {
      this.payment();
    }
    console.log(this.isShoppingComplete);
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad PaymentPage');
    this.initializeBackButtonCustomHandler();
    this.navBar.backButtonClick = (e: UIEvent) => {
      // todo something
      this.navCtrl.popToRoot();
    }
  }
  ionViewWillLeave() {
    this.unregisterBackButtonAction && this.unregisterBackButtonAction();
  }
  initializeBackButtonCustomHandler(): void {
    this.unregisterBackButtonAction = this.platform.registerBackButtonAction(() => {
      let activePortal = this.ionicApp._loadingPortal.getActive() || this.ionicApp._modalPortal.getActive() ||
        this.ionicApp._toastPortal.getActive() || this.ionicApp._overlayPortal.getActive();
      if (!activePortal) {
        this.navCtrl.popToRoot();
      }
    }, 1);
  }

  refreshPage(payment_id?) {
    this.loader.show();
    if (payment_id) {
      this.WC.updateOrder(this.orderDetails.id, "processing", payment_id).then((res) => {
        this.orderDetails = res;
        this.loader.dismiss();
      }, err => {
        console.log(err);
        this.toast.showError();
        this.loader.dismiss();
      });
    } else {
      this.WC.getOrder(this.orderDetails.id).then((res) => {
        this.orderDetails = res;
        this.loader.dismiss();
      }, err => {
        console.log(err);
        this.toast.showError();
        this.loader.dismiss();
      });
    }

  }

  goToHome() {
    this.navCtrl.popToRoot();
  }
  payment() {
    if (this.orderDetails.status && this.orderDetails.status === 'failed') {
      return;
    }


    let payment_method = "?payment_method=" + this.orderDetails.payment_method;
    let order_id = "&ORDER_ID=" + this.orderDetails.id;
    let cus_id = "&CUST_ID=" + this.orderDetails.customer_id;
    let payment_url = App.url + "/wp-json/wc/v2/payment" + payment_method + order_id + cus_id;



    let browser;

    switch (this.orderDetails.payment_method) {
      case 'razorpay':
        this.razorpayCheckout();
        break;
      case 'paytm':
        this.paytmCheckout();
        // browser = this.iab.create(payment_url,
        //   "_self", { location: 'no', clearcache: 'yes', clearsessioncache: 'yes', hidden: 'yes' });
        // browser.on("loadstart").subscribe(event => {
        //   if (event.url.includes('/order-received')) {
        //     browser.close();
        //   }
        // });
        // browser.on("loadstop").subscribe(event => {
        //   if (event.url.includes('paytm.com') || event.url.includes('paytm.in')) {
        //     browser.show();
        //     this.loader.dismiss();
        //   }
        // });
        break;
      case 'wallet':
        this.translate.get(['PAYMENT_LOADING']).subscribe(x => {
          this.loader.showWithMessage(x.PAYMENT_LOADING);
        });
        browser = this.iab.create(App.url + "/wp-json/wc/v2/wallet/payment" + payment_method + order_id + cus_id,
          "_self", { location: 'no', clearcache: 'yes', clearsessioncache: 'yes' });
        browser.on("loadstart").subscribe(event => {
          this.loader.dismiss();
          console.log(event);
        });
        browser.on("loadstop").subscribe(event => {
          browser.close();
        });
        browser.on("exit").subscribe(event => {
          console.log(event);
          this.refreshPage();
        });
        break;
      case 'pumcp':
        this.translate.get(['PAYMENT_LOADING']).subscribe(x => {
          this.loader.showWithMessage(x.PAYMENT_LOADING);
        });
        browser = this.iab.create(payment_url,
          "_self", { location: 'no', clearcache: 'yes', clearsessioncache: 'yes', hidden: 'yes' });
        let openpumcp = false;
        browser.on("loadstop").subscribe(event => {
          if (event.url.includes('payu')) {
            browser.show();
            openpumcp = true;
            this.loader.dismiss();
          }
          if (event.url.includes(App.url) && openpumcp) {
            browser.close();
          }
        });
        browser.on("exit").subscribe(event => {
          console.log(event);
          this.refreshPage();
        });
        break;
      case 'instamojo':
        this.translate.get(['PAYMENT_LOADING']).subscribe(x => {
          this.loader.showWithMessage(x.PAYMENT_LOADING);
        });
        browser = this.iab.create(payment_url,
          "_self", { location: 'no', clearcache: 'yes', clearsessioncache: 'yes', hidden: 'yes' });
        browser.show();
        this.loader.dismiss();
        browser.on("loadstart").subscribe(event => {
          if (event.url.includes("/order-received")) {
            browser.close();
          }
        });
        browser.on("exit").subscribe(event => {
          console.log(event);
          this.refreshPage();
        });
        break;
      case 'paypal':
        this.translate.get(['PAYMENT_LOADING']).subscribe(x => {
          this.loader.showWithMessage(x.PAYMENT_LOADING);
        });
        browser = this.iab.create(payment_url,
          "_self", { location: 'no', clearcache: 'yes', clearsessioncache: 'yes', hidden: 'yes' });
        browser.on("loadstart").subscribe(event => {
          if (event.url.includes('/order-received')) {
            browser.close();
          }
        });
        browser.on("loadstop").subscribe(event => {
          if (event.url.includes('/order-pay')) {
            browser.show()
            this.loader.dismiss();
          }
        });
        browser.on("exit").subscribe(event => {
          console.log(event);
          this.refreshPage();
        });
        break;
    }
  }
  paytmCheckout() {
    let txnRequest = {
      "MID": "oGoTrn55843513837534",                  // PayTM Credentials
      "ORDER_ID": this.orderDetails.id,      //Should be unique for every order.
      "CUST_ID": this.orderDetails.customer_id,
      "INDUSTRY_TYPE_ID": "Retail",       // PayTM Credentials
      "CHANNEL_ID": "WAP",                // PayTM Credentials
      "TXN_AMOUNT": this.orderDetails.total, // Transaction Amount should be a String
      "WEBSITE": "DEFAULT",            // PayTM Credentials
      "CALLBACK_URL": "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=" + this.orderDetails.total
    }
    this.loader.showWithMessage("Generating Checksum");
    this.WC.generateChecksumAPI(txnRequest).subscribe((res: any) => {
      this.loader.dismiss();
      console.log(res);
      txnRequest['CHECKSUMHASH'] = res.CHECKSUMHASH;
      txnRequest["ENVIRONMENT"] = "production";
      paytm.startPayment(txnRequest, (response: any) => {
        console.log(response);
        if (response.STATUS == "TXN_SUCCESS") {
          this.refreshPage(response.TXNID);
        } else {
          alert("Transaction Failed for reason: - " + response.RESPMSG + " (" + response.RESPCODE + ")");
        }
      }, error => {
        alert("Transaction Failed for reason: - " + error.RESPMSG + " (" + error.RESPCODE + ")");
        console.log(error);
      });
    }, err => {
      this.loader.dismiss();
      alert('Error Generating Checksum ' + err);
    });
  }

  razorpayCheckout() {
    let options = {
      description: 'Order  ' + this.orderDetails.id,
      image: '',
      currency: this.orderDetails.currency,
      key: 'rzp_live_Mw6y1rFt9AqPGr',
      amount: parseFloat(this.orderDetails.total) * 100,
      name: App.store,
      prefill: {
        email: this.orderDetails.billing.email || '',
        contact: this.orderDetails.billing.phone || '',
        name: this.orderDetails.billing.first_name + ' ' + this.orderDetails.billing.last_name
      },
      theme: {
        color: this.settings.all.appSettings.primary_color
      },
      modal: {
        ondismiss: function () {
          alert('Payment Cancelled');
        }
      }
    };
    RazorpayCheckout.open(options, (payment_id) => {
      //alert('payment_id: ' + payment_id);
      this.refreshPage(payment_id);
    }, (error) => {
      //alert(error.description + ' (Error ' + error.code + ')');
      this.refreshPage();
    });
  }
  calculatePrice(x) {
    return (x.prices_include_tax ? x.total : (Number(x.total) + Number(x.total_tax)).toFixed(2));
  }
}
