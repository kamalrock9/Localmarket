import { TranslateService } from '@ngx-translate/core';
import { Component, NgZone, ViewChild } from '@angular/core';
import { IonicPage, NavController, NavParams, AlertController, ModalController, Events, Content } from 'ionic-angular';
import { RestProvider, ToastProvider, UserProvider, SettingsProvider, LoadingProvider } from '../../providers/providers';

@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-cart',
  templateUrl: 'cart.html',
})
export class CartPage {
  @ViewChild(Content) content: Content;
  cart: any = {};
  inputCoupon: boolean = false;
  pakage: any = {};
  objectKeys = Object.keys;
  constructor(public navCtrl: NavController, public restClient: RestProvider, private loader: LoadingProvider, private user: UserProvider,
    private toast: ToastProvider, public zone: NgZone, public navParams: NavParams, public translate: TranslateService,
    private alertCtrl: AlertController, private modalCtrl: ModalController, private events: Events, public settings: SettingsProvider) {
    if (this.user.shipping) {
      this.pakage.country = this.user.shipping.country || "IN";
      this.pakage.state = this.user.shipping.state || null;
      this.pakage.postcode = this.user.shipping.postcode || null;
    } else {
      this.pakage.country = settings.country || "IN";
      this.pakage.state = settings.state || null;
      this.pakage.postcode = settings.postcode || null;
    }
    this.getCart();
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad CartPage');
  }

  getCart(method?) {
    //this.loader.show();
    this.restClient.getCart(this.pakage, method).then(
      (res) => {
        console.log(res);
        let data = JSON.parse(res.data);
        //this.zone.run(() => {
        this.cart = {};
        setTimeout(() => {
        //this.loader.dismiss();
          this.cart = data;
          this.content.resize();
        }, 10);
        // });
        
      }).catch(err => {
        console.log(err);
        this.loader.dismiss();
        this.translate.get(['ERROR']).subscribe(x => {
          this.toast.show(x.ERROR);
        });
      });
  }

  removeCart(cart_item_key, i) {
    this.translate.get(['REMOVE_FROM_CART', 'ARE_YOU_SURE', 'NO', 'YES']).subscribe((x) => {
      this.alertCtrl.create({
        title: x.REMOVE_FROM_CART,
        message: x.ARE_YOU_SURE,
        buttons: [{
          text: x.NO
        }, {
          text: x.YES,
          handler: () => {
            this.loader.show();
            this.restClient.removeCartItem(this.pakage, cart_item_key).then(
              (res: any) => {
                this.loader.dismiss();
                this.events.publish("cartchanged");
                let data = JSON.parse(res.data);
                console.log(data);
                //this.zone.run(() => {
                this.cart = {};
                setTimeout(() => {
                  this.cart = data;
                  this.content.resize();
                }, 10);
                this.content.resize();
                //});
              }).catch(err => {
                console.log(err);
                this.loader.dismiss();
                this.translate.get(['ERROR']).subscribe(x => {
                  this.toast.show(x.ERROR);
                });

              });
          }
        }]
      }).present();
    });
  }

  removeCoupon(code) {
    this.translate.get(['REMOVE_FROM_COUPON', 'ARE_YOU_SURE', 'NO', 'YES']).subscribe((x) => {
      this.alertCtrl.create({
        title: x.REMOVE_FROM_COUPON,
        message: x.ARE_YOU_SURE,
        buttons: [{
          text: x.NO
        }, {
          text: x.YES,
          handler: () => {
            this.loader.show();
            this.restClient.removeCoupon(this.pakage, code).then(
              (res: any) => {
                this.loader.dismiss();
                let data = JSON.parse(res.data);
                console.log(data);
                //this.zone.run(() => {
                this.cart = {};
                setTimeout(() => {
                  this.cart = data;
                  this.content.resize();
                }, 10);
                this.content.resize();
                //});
              }).catch(err => {
                this.loader.dismiss();
                console.log(err);
                this.translate.get(['ERROR']).subscribe(x => {
                  this.toast.show(x.ERROR);
                });
              });
          }
        }]
      }).present();
    });
  }
  decreaseQuantity(i) {
    if (this.cart.cart_data[i].quantity > 1) {
      this.loader.show();
      this.restClient.updateCart(this.pakage, this.cart.cart_data[i].cart_item_key, --this.cart.cart_data[i].quantity)
        .then((res: any) => {
          this.loader.dismiss();
          this.events.publish("cartchanged");
          let data = JSON.parse(res.data);
          console.log(data);
          //this.zone.run(() => {
          this.cart = {};
          setTimeout(() => {
            this.cart = data;
            this.content.resize();
          }, 10);
          this.content.resize();
          // });
        }).catch(err => {
          console.log(err);
          this.loader.dismiss();
          this.translate.get(['ERROR']).subscribe(x => {
            this.toast.show(x.ERROR);
          });
        });
    }
    else {
      this.translate.get(['MIN_ITEM']).subscribe(x => {
        this.toast.show(x.MIN_ITEM);
      });
    }

  }
  increaseQuantity(i) {
    let quantity: number = this.cart.cart_data[i].quantity;
    if (this.cart.cart_data[i].manage_stock) {
      if (quantity < this.cart.cart_data[i].stock_quanity) {
        quantity++;
      } else {
        this.translate.get(['MAX_ITEM']).subscribe(x => {
          this.toast.show(x.MAX_ITEM);
        });
      }
    } else {
      quantity++;
    }
    if (quantity != Number(this.cart.cart_data[i].quantity)) {
      this.loader.show();
      this.restClient.updateCart(this.pakage, this.cart.cart_data[i].cart_item_key, quantity)
        .then((res: any) => {
          this.events.publish("cartchanged");
          this.loader.dismiss();
          let data = JSON.parse(res.data);
          console.log(data);
          //this.zone.run(() => {
          this.cart = {};
          setTimeout(() => {
            this.cart = data;
            this.content.resize();
          }, 10);
          //});
          console.log(this.cart);
        }).catch(err => {
          this.loader.dismiss();
          console.log(err);
          this.translate.get(['ERROR']).subscribe(x => {
            this.toast.show(x.ERROR);
          });
        });
    }
  }
  showSearch() {
		let modal = this.modalCtrl.create('SearchPage', {});
		modal.onDidDismiss((data) => {
			if (data && data.page) {
				this.goTo(data.page, data.params);
			}
		});
		modal.present();
	} 

  calculateShipping() {
    let modal = this.modalCtrl.create('CalculateShippingPage', { params: this.pakage }, { cssClass: 'shipping' });
    modal.onDidDismiss(data => {
      if (data != null) {
        this.pakage = data;
        if (data.postcode !== '') {
          this.settings.setSettings(data.country, 'country');
          this.settings.setSettings(data.state, 'state');
          this.settings.setSettings(data.postcode, 'postcode');
        }
        console.log(this.pakage)
        this.getCart();
      }
      console.log(this.pakage);
    });
    modal.present();
  }

  goTo(page, params) {
    this.navCtrl.push(page, { params: params });
  }
  checkout() {
    if (this.user.all) {
      this.goTo('CheckoutPage', {});
    } else {
      this.translate.get(['LOGIN', 'LOGIN_FIRST', 'REGISTER']).subscribe((x) => {
        this.alertCtrl.create({
          title: x.LOGIN,
          message: x.LOGIN_FIRST,
          cssClass: "custom_alert",
          buttons: [{
            text: x.LOGIN,
            handler: () => {
              let modal = this.modalCtrl.create('LoginPage', { page: "login" });
              modal.onDidDismiss((data) => {
                if (data & data.code)
                  this.checkout();
              });
              modal.present();
            }
          }, {
            text: x.REGISTER,
            handler: () => {
              let modal = this.modalCtrl.create('LoginPage', { page: "signup" });
              modal.onDidDismiss((data) => {
                if (data & data.code)
                  this.checkout();
              });
              modal.present();
            }
          }]
        }).present();
      });
    }
  }
  shipping_price() {
    return this.cart.shipping_method.find(x => x.id == this.cart.chosen_shipping_method).shipping_method_price;
  }
  couponToggle() {
    let modal = this.modalCtrl.create('CouponPage', { pakage: this.pakage, appliedCoupon: this.cart.coupon }, { cssClass: 'coupon' });
    modal.onDidDismiss((data) => {
      if (data) {
        this.cart = {};
        setTimeout(() => {
          this.cart = data;
          this.content.resize();
        }, 10);
      }
    });
    modal.present();
  }
}
