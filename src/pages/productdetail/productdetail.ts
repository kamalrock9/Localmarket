import { PhotoViewer } from '@ionic-native/photo-viewer';
import { InAppBrowser } from '@ionic-native/in-app-browser';
import { TranslateService } from '@ngx-translate/core';
import { Component, ViewChild } from '@angular/core';
import { IonicPage, NavController, NavParams, Platform, AlertController, Events, ModalController, Slides } from 'ionic-angular';
import { WooCommerceProvider, WishlistProvider, ToastProvider, SettingsProvider, LoadingProvider, RestProvider } from '../../providers/providers';
import { SocialSharing } from '@ionic-native/social-sharing';
import { NgZone } from '@angular/core';


@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-productdetail',
  templateUrl: 'productdetail.html'
})
export class ProductdetailPage {
  @ViewChild('slider') slider: Slides;
  product: any;
  postcode: string;
  postcodeEnter: boolean = true;
  newPostCode: string;
  deliveryDetails: any = {};
  initial_img_src: string;
  pattern: any = /\[.+\]/g;
  dir: string;

  constructor(public navCtrl: NavController, public navParams: NavParams, private WC: WooCommerceProvider,
    public wishlist: WishlistProvider, private toast: ToastProvider, private loader: LoadingProvider,
    private restClient: RestProvider, private translate: TranslateService, private platform: Platform,
    private alertCtrl: AlertController, private socialSharing: SocialSharing, private events: Events,
    private iab: InAppBrowser, public settings: SettingsProvider, private zone: NgZone, private modalCtrl: ModalController,
    private photoViewer: PhotoViewer) {

    this.dir = platform.dir();
    //this.product = JSON.parse(JSON.stringify(this.navParams.get('params')));
    // this.product = this.navParams.get('params'); 
    this.events.subscribe('Loaded Product', () => {
      this.setupProduct();
    });
    console.log(this.navParams.data.params);
    if (this.navParams.data.params && this.navParams.data.params.isReferedByPush) {
      console.log("Push");
      this.WC.getProductById(null, this.navParams.data.params.id).subscribe((res) => {
        if (res) {
          console.log(res);
          this.product = res;
          this.events.publish('Loaded Product');
        } else {
          this.toast.show("Something wrong from server");
          this.navCtrl.pop();
        }
      }, err => {
        this.toast.showError();
        this.navCtrl.pop();
      });
    } else if (this.navParams.data.params && this.navParams.data.params.isReferedByDeeplinks) {
      console.log("Deeplinks");
      this.loader.show();
      this.WC.getProductByUrl(this.navParams.data.params.link).subscribe((res) => {
        if (res) {
          this.product = res;
          this.events.publish('Loaded Product');
        } else {
          this.toast.showError();
          this.navCtrl.pop();
        }
      }, err => {
        this.toast.showError();
        this.navCtrl.pop();
      });
    } else {
      console.log("default");
      this.product = this.navParams.data.params;
      this.events.publish('Loaded Product');
    }

  }
  setupProduct() {
    if (!this.product.var_attributes) {
      this.product.var_attributes = [];
      for (let at of this.product.attributes) {
        if (at.variation) {
          this.product.var_attributes.push(at);
        }
      }
    }
    if (!this.product.attr) {
      this.product.attr = {};
    }

    this.postcode = this.settings.postcode;
    if (this.postcode && this.settings.all.appSettings.pincode_active) {
      this.submitPincodeCheck(this.postcode);
    }
    if (this.product.type == 'variable' || this.product.type == 'simple') {
      this.product.quantity = 1;
    }
    if (this.product.related_ids.length > 0 && !this.product.related) {
      this.WC.getProductById(this.product.related_ids.join()).subscribe((x) => {
        this.zone.run(() => {
          this.product.related = x;
        });
      });
    }
    if (this.product.upsell_ids.length > 0 && !this.product.upsell) {
      this.WC.getProductById(this.product.upsell_ids.join()).subscribe((x) => {
        this.zone.run(() => {
          this.product.upsell = x;
        });
      });
    }
    if (this.product.grouped_products.length > 0 && !this.product.grouped_products[0].name) {
      console.log(this.product.grouped_products);
      this.WC.getProductById(this.product.grouped_products.join()).subscribe((x: any) => {
        x.map((element) => {
          element.quantity = 0;
        });
        this.zone.run(() => {
          this.product.grouped_products = x;
        });
      });
    }
    if (this.initial_img_src) {
      this.product.images[0].src = this.initial_img_src;
    }
    console.log(this.product);
  }
  ionViewDidLoad() {
    console.log('ionViewDidLoad: ProductdetailPage');
  }
  ionViewDidEnter() {
    if (this.product) {
      this.events.publish('view:enter', 'Single Product Page - ' + this.product.name);
    } else {
      this.events.publish('view:enter', 'Single Product Page - ');
    }
  }

  loadVariation(data) {
    this.loader.show();
    console.log("Loading Variation");
    this.WC.getProductVariation(data).subscribe((res: any) => {
      this.loader.dismiss();
      console.log(res);
      if (!res.error) {
        this.setVariation(res);
      } else {
        this.product.issetVariation = false;
        this.toast.showWithClose("Currently This variation is not available. Select a different Variation");
      }
    }, (err) => {
      console.log(err);
      this.toast.showError();
      this.loader.dismiss();
      this.product.issetVariation = false;
    });

  }
  setVariation(x) {
    if (x.image.src) {
      this.product.images[0].src = x.image.src;
    }
    this.slider.slideTo(0);
    this.product.variation_id = x.id;
    this.product.price = x.price;
    this.product.price_html = x.price_html;
    this.product.regular_price = x.regular_price;
    this.product.on_sale = x.on_sale;
    this.product.in_stock = x.in_stock;
    this.product.variation_selected = x;
    this.product.quantity = 1;
    this.product.issetVariation = true;
  }

  setFav(product: any) {
    this.wishlist.add(product);
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
  goTo(page, params) {
    this.navCtrl.push(page, { params: params }, { animate: false });
  }

  onCheckChange(e, item) {
    console.log(e.checked);
    if (e.checked) {
      item.quantity = 1;
    } else {
      item.quantity = 0;
    }
  }
  decreaseQuantity(i?: number) {
    //console.log(i);
    if (this.product.type == 'grouped') {
      if (this.product.grouped_products[i].quantity > 0) {
        this.product.grouped_products[i].quantity--;
      }
    } else {
      if (this.product.quantity > 1) {
        this.product.quantity--;
      }
    }
  }
  increaseQuantity(i?: number) {
    this.translate.get(['NO_MORE_ADD']).subscribe((x) => {
      if (this.product.type == 'grouped') {
        this.product.grouped_products[i].quantity++;
        console.log(this.product.grouped_products[i].quantity);
      } else if (this.product.type == 'variable') {
        if (this.product.issetVariation) {
          if (this.product.variation_selected.manage_stock == 'parent') {
            if (this.product.manage_stock) {
              if (this.product.quantity < this.product.stock_quantity) {
                this.product.quantity++;
              } else {
                this.toast.show(x.NO_MORE_ADD);
              }
            } else {
              this.product.quantity++;
            }
          } else if (this.product.variation_selected.manage_stock) {
            if (this.product.quantity < this.product.variation_selected.stock_quantity) {
              this.product.quantity++;
            } else {
              this.toast.show(x.NO_MORE_ADD);
            }
          } else {
            this.product.quantity++;
          }
        } else {
          this.translate.get(['VALID_VARIATION']).subscribe(x => {
            this.toast.show("Select a valid variation");
          });
        }

      } else {
        if (this.product.manage_stock) {
          if (this.product.quantity < this.product.stock_quantity) {
            this.product.quantity++;
          } else {
            this.toast.show(x.NO_MORE_ADD);
          }
        } else {
          this.product.quantity++;
        }
      }
    });
  }
  onChange() {
    if (Object.keys(this.product.attr).length == this.product.var_attributes.length) {
      let data = {
        product_id: this.product.id,
        attributes: this.product.attr
      }
      console.log(data);
      this.loadVariation(data);
    }
  }

  addToCart(isBuyNow?) {
    this.translate.get(['PINCODE', 'NO_DELIVERY', 'SELECT_ONE_PRODUCT', 'SELECT_PRODUCT_QUANTITY', 'VALID_VARIATION']).subscribe(x => {
      if (this.settings.all.appSettings.pincode_active) {
        if (!this.postcode) {
          this.toast.show(x.PINCODE);
          return;
        }
        if (this.postcode && !this.deliveryDetails.delivery) {
          this.toast.show(x.NO_DELIVERY);
          return;
        }
      }

      let data: any = {
        id: this.product.id
      }

      if (this.product.type == 'grouped') {
        if (this.product.grouped_products.every((element) => { return (element.quantity == 0); })) {
          this.toast.show(x.SELECT_ONE_PRODUCT);
          return;
        }
        data.quantity = {};
        for (let i in this.product.grouped_products) {
          if (this.product.grouped_products[i].quantity > 0) {
            data.quantity[this.product.grouped_products[i].id] = this.product.grouped_products[i].quantity;
          }
        }

      }
      else if (this.product.type == 'simple') {
        if (!this.product.quantity || this.product.quantity == 0) {
          this.toast.show(x.SELECT_PRODUCT_QUANTITY);
          return;
        }
        data.quantity = this.product.quantity;
      } else {
        data.quantity = this.product.quantity;
        if (this.product.issetVariation) {
          data.variation_id = this.product.variation_id;
          data.variation = this.product.attr;
          //let temp = this.product.variation_selected.permalink.substring(this.product.variation_selected.permalink.lastIndexOf("?") + 1).split('&');
        } else {
          this.toast.show(x.VALID_VARIATION);
          return;
        }
      }
      console.log(data);
      this.loader.show();
      this.restClient.addToCart(data).then((res: any) => {
        this.loader.dismiss();
        console.log(res);

        let data = JSON.parse(res.data);
        let msg = (data instanceof Array) ? data.map(e => e.message).join(", ") : data.message;
        if (this.isError(data)) {
          this.toast.showWithClose(msg);
        } else {
          if (isBuyNow) {
            this.goTo("CartPage", "");
          } else {
            this.viewCart(msg);
          }
          this.events.publish("cartchanged");
        }
      }).catch(
        err => {
          console.log(err);
          this.loader.dismiss();
        });
    });
  }

  share(product: any) {
    if (!this.platform.is('cordova')) {
      this.translate.get(['OK', 'ONLY_DEVICE', 'ONLY_DEVICE_DESC']).subscribe(x => {
        this.alertCtrl.create({
          title: x.ONLY_DEVICE,
          message: x.ONLY_DEVICE_DESC,
          buttons: [{
            text: x.OK
          }]
        }).present();
        return false;
      });

    } else {
      let img = [];
      for (let i in product.images)
        img.push(product.images[i].src);
      this.socialSharing.share(product.name, product.name, img, product.permalink).then((x) => {
        console.log(x);
        this.translate.get(['SHARED']).subscribe(x => {
          this.toast.show('Successfully shared');
        });
      }).catch((err) => {
        console.log(err);
      });
    }
  }
  buyExternal() {
    this.iab.create(this.product.external_url, '_system')
  }
  submitPincodeCheck(newPostCode?) {
    if (!this.postcodeEnter) {
      this.postcodeEnter = true;
      return;
    }
    this.loader.show();
    this.WC.checkPincode(newPostCode, this.product.id).subscribe((res) => {
      this.loader.dismiss();
      console.log(res);
      this.postcode = newPostCode;
      this.postcodeEnter = false;
      console.log(this.postcode);
      this.settings.setSettings(this.postcode, 'postcode');
      this.deliveryDetails = res;
    }, err => {
      this.loader.dismiss();
      this.toast.showError();
    });
  }
  viewCart(data) {
    let modal = this.modalCtrl.create('MiniCartPage', { params: data }, {
      enterAnimation: 'modal-slide-in',
      leaveAnimation: 'modal-slide-out',
      cssClass: 'mini-cart'
    });
    modal.onDidDismiss((action) => {
      if (action) {
        if (action && action == 'root') {
          this.navCtrl.popToRoot();
        } else if (action == 'back') {
          this.navCtrl.pop();
        } else if (action == 'cart') {
          this.goTo('CartPage', '');
        }
      }
    })
    modal.present();

  }
  isError(data) {
    if (data instanceof Array) {
      return data.every(e => e.code === '0');
    } else {
      return (data.code == 0);
    }
  }
  zoomImage(src, name) {
    this.photoViewer.show(src, name, { share: false });
  }
}