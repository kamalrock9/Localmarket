import { Events } from 'ionic-angular/util/events';
import { RestProvider } from './../../providers/rest/rest';
import { LoadingProvider } from './../../providers/loading/loading';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController } from 'ionic-angular';
import { WooCommerceProvider, WishlistProvider, ToastProvider, SettingsProvider } from '../../providers/providers';
import { PageTrack } from '../../decorator/page-track.decorator';
import { TranslateService } from '@ngx-translate/core';

@PageTrack({})
@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-product',
  templateUrl: 'product.html',
})
export class ProductPage {
  products: Array<any>;
  params: any = {};
  page: number = 1;
  hasMore: boolean = false;
  sortby: string = 'popularity';
  per_page: number = 30;
  show_loader: boolean = true;
  showEmpty: boolean = false;
  filterData: any = {};
  items: Array<any>;
  categories: Array<any>;
  on_sale: boolean;
  featured: boolean;

  constructor(public navCtrl: NavController, public navParams: NavParams, private WC: WooCommerceProvider,
    public wishlist: WishlistProvider, private zone: NgZone, private toast: ToastProvider, private translate: TranslateService,
    public settings: SettingsProvider, private modalCtrl: ModalController, private loader: LoadingProvider, private restClient: RestProvider,
    private events: Events) {

    this.translate.get(['CATEGORIES', 'PRICE']).subscribe((x) => {
      this.items = [
        { name: x.PRICE, slug: "native_price", min_price: this.settings.appSettings.price.min || 0, max_price: this.settings.appSettings.price.max || '' },
        { name: x.CATEGORIES, slug: "product_cat" },
        { name: 'Brands', slug: "brands" }
      ];
    });
    this.items[1].id = this.navParams.data.params.id;
    this.items[1].cat_name = this.navParams.data.params.name;
    this.items[2].id = this.navParams.data.params.brand_id || '';

    this.params.search = this.navParams.data.params.search_data;
    this.sortby = this.navParams.data.params.sortby || "popularity";
    this.on_sale = this.navParams.data.params.on_sale || false;
    this.featured = this.navParams.data.params.featured || false;

    // console.log(this.settings.category);

    if (this.items[1].id && this.settings.category && this.settings.category.length > 0) {
      this.categories = this.settings.category.filter((item) => {
        return item.parent == this.items[1].id;
      });
      console.log(this.categories);
    } else if (this.items[1].id) {
      this.WC.getAllCategories(this.items[1].id).subscribe((res: any) => {
        this.categories = res;
      }, err => {
        console.log(err);
      })
    }
    this.loadProducts();
    // this.WC.getCustomAttributes().subscribe((res: any) => {
    //   console.log(res);
    //   res.forEach(element => {
    //     element.active = false;
    //   });
    //   //this.items.length = 2;
    //   this.items = this.items.concat(res);
    //   console.log(this.items);
    // });
  }



  ionViewDidLoad() {
    console.log('ionViewDidLoad ProductPage');
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

  loadProducts() {
    this.show_loader = true;
    this.products = [];
    this.WC.getCustomProducts(this.page, this.per_page, this.sortby, this.items[1].id, this.items[2].id, this.params.search, this.items[0].min_price, this.items[0].max_price, this.on_sale, this.featured, this.filterData).subscribe((res: any) => {
      this.show_loader = false;
      console.log(res);
      this.zone.run(() => {
        res.forEach(element => {
          element.var_attributes = [];
          element.attr = {};
          if (element.type == 'variable' || element.type == 'simple') {
            element.quantity = 1;
          }
          for (let at of element.attributes) {
            if (at.variation) {
              element.var_attributes.push(at);
            }
          }
        });
        this.products = res;
        this.hasMore = (res.length == this.per_page);
        this.showEmpty = (res.length == 0);

        let p = this.joinProductIds(this.products);
        this.WC.getCustomAttributes(p).subscribe((res: any) => {
          res.forEach(element => {
            element.active = false;
          });
          let newFilter = [];
          for (let i = 0; i < res.length; i++) {
            let flag = 0;
            for (let j = 3; j < this.items.length; j++) {
              //console.log(this.items[j].id);
              if (res[i].id == this.items[j].id) {
                flag = j;
                break;
              }
            }
            if (flag > 0) {
              newFilter.push(this.items[flag]);
            } else {
              newFilter.push(res[i]);
            }
            // newFilter.sort((a, b)=> { 
            //   return a.id - b.id;
            // });
          }
          this.items.length = 3;
          this.items = this.items.concat(newFilter);
          console.log(this.items);
        });
      });
    }, (err) => {
      this.toast.showError();
      this.show_loader = false;
      console.log(err);
    });
  }
  loadMoreProducts(event) {
    this.page++;
    this.WC.getCustomProducts(this.page, this.per_page, this.sortby, this.items[1].id, this.items[2].id, this.params.search, this.items[0].min_price, this.items[0].max_price, this.on_sale, this.featured, this.filterData).subscribe((res: any) => {
      console.log(res);
      this.zone.run(() => {
        res.forEach(element => {
          element.var_attributes = [];
          element.attr = {};
          if (element.type == 'variable' || element.type == 'simple') {
            element.quantity = 1;
          }
          for (let at of element.attributes) {
            if (at.variation) {
              element.var_attributes.push(at);
            }
          }
        });
        this.products = this.products.concat(res);

        let p = this.joinProductIds(this.products);
        this.WC.getCustomAttributes(p).subscribe((res: any) => {
          let newFilter = [];
          for (let i = 0; i < res.length; i++) {
            let flag = 0;
            for (let j = 3; j < this.items.length; j++) {
              //console.log(this.items[j].id);
              if (res[i].id == this.items[j].id) {
                flag = j;
                break;
              }
            }
            if (flag > 0) {
              newFilter.push(this.items[flag]);
            } else {
              newFilter.push(res[i]);
            }
          }
          this.items.length = 3;
          this.items = this.items.concat(newFilter);
          console.log(this.items);
        });

        event.complete();
        if (res.length == this.per_page) {
          this.hasMore = true;
        } else {
          this.hasMore = false;
          event.enable(false);
        }
      }, (err) => {
        this.toast.showError();
        console.log(err);
      });
    });
  }

  setFav(product: any) {
    //  this.translate.get(['REMOVE_WISH', 'ADDED_WISH']).subscribe( x=> {
    this.wishlist.add(product);
    // });
  }

  sort() {
    let modal = this.modalCtrl.create("SortPage", { params: this.sortby }, {
      enterAnimation: 'modal-slide-in',
      leaveAnimation: 'modal-slide-out',
      cssClass: 'sort'
    });
    modal.onDidDismiss((x) => {
      console.log(x);
      if (x) {
        this.sortby = x;
        this.page = 1;
        this.loadProducts();
      }
    });
    modal.present();

  }
  joinProductIds(p) {
    let arr = [];
    for (let i of p) {
      if (i.attributes.length > 0) {
        arr.push(i.id);
      }
    }
    return arr.join(",");
  }
  filter(active) {
    let modal = this.modalCtrl.create("FilterPage", { data: this.items, active: active },
      {
        enterAnimation: 'modal-slide-in',
        leaveAnimation: 'modal-slide-out',
        cssClass: 'filter'
      });
    modal.present();
    modal.onDidDismiss((data) => {
      if (data) {
        this.items = data;
        console.log(this.items);
        this.page = 1;
        this.filterData = {};
        for (let i = 2; i < this.items.length; i++) {
          let tmp = [];
          for (let j in this.items[i].options) {
            if (this.items[i].options[j].checked)
              tmp.push(this.items[i].options[j].slug);
          }
          if (tmp.length > 0)
            this.filterData[this.items[i].slug] = tmp;
        }
        console.log(this.filterData);
        this.loadProducts();
      }
    })
  }

  goTo(page, params) {
    this.navCtrl.push(page, { params: params }, { animate: false });
  }
  onChange(p) {
    if (Object.keys(p.attr).length == p.var_attributes.length) {
      let data = {
        product_id: p.id,
        attributes: p.attr
      }
      console.log(data);
      this.loadVariation(p, data);
    }
  }
  increaseQuantity(p) {
    this.translate.get(['NO_MORE_ADD']).subscribe((x) => {
      if (p.type == 'variable') {
        if (p.isSetVariation) {
          if (p.variation_selected.manage_stock == 'parent') {
            if (p.manage_stock) {
              if (p.quantity < p.stock_quantity) {
                p.quantity++;
              } else {
                this.toast.show(x.NO_MORE_ADD);
              }
            } else {
              p.quantity++;
            }
          } else if (p.variation_selected.manage_stock) {
            if (p.quantity < p.variation_selected.stock_quantity) {
              p.quantity++;
            } else {
              this.toast.show(x.NO_MORE_ADD);
            }
          } else {
            p.quantity++;
          }
        } else {
          this.translate.get(['VALID_VARIATION']).subscribe(x => {
            this.toast.show("Select a valid variation");
          });
        }

      } else {
        if (p.manage_stock) {
          if (p.quantity < p.stock_quantity) {
            p.quantity++;
          } else {
            this.toast.show(x.NO_MORE_ADD);
          }
        } else {
          p.quantity++;
        }
      }
    });
  }
  decreaseQuantity(p) {
    if (p.quantity > 1) {
      p.quantity--;
    }
  }
  loadVariation(p, data) {
    this.loader.show();
    console.log("Loading Variation");
    this.WC.getProductVariation(data).subscribe((res: any) => {
      this.loader.dismiss();
      console.log(res);
      if (!res.error) {
        p.variation_id = res.id;
        p.price = res.price;
        p.price_html = res.price_html;
        p.regular_price = res.regular_price;
        p.on_sale = res.on_sale;
        p.in_stock = res.in_stock;
        p.variation_selected = res;
        p.quantity = 1;
        p.isSetVariation = true;
      } else {
        p.isSetVariation = false;
        this.toast.showWithClose("Currently This variation is not available. Select a different Variation");
      }
    }, (err) => {
      console.log(err);
      this.toast.showError();
      this.loader.dismiss();
      p.isSetVariation = false;
    });

  }
  addToCart(p) {
    let data: any = {
      id: p.id,
      quantity: p.quantity
    }
    if (p.type == 'variable') {
      if (p.isSetVariation) {
        data.variation_id = p.variation_id;
        data.variation = p.attr;
      } else {
        this.toast.show("Please Select a valid variation");
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
        this.toast.show(msg)
        this.events.publish("cartchanged");
      }
    }).catch(
      err => {
        console.log(err);
        this.loader.dismiss();
      });
  }
  isError(data) {
    if (data instanceof Array) {
      return data.every(e => e.code === '0');
    } else {
      return (data.code == 0);
    }
  }
}
