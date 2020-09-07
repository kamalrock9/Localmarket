import { App } from "./../../app/app.config";
import { Component, ViewChild, NgZone } from "@angular/core";
import {
  NavController,
  IonicPage,
  ModalController,
  Platform,
  Slides,
  NavParams,
  Content,
} from "ionic-angular";
import {
  WooCommerceProvider,
  LoadingProvider,
  WishlistProvider,
  ToastProvider,
  SettingsProvider,
  UserProvider,
} from "../../providers/providers";
import { SplashScreen } from "@ionic-native/splash-screen";
import { PageTrack } from "../../decorator/page-track.decorator";
import { AdMobFree } from "@ionic-native/admob-free";
import { Deeplinks } from "@ionic-native/deeplinks";
import { TranslateService } from "@ngx-translate/core";
import { InAppBrowser } from "@ionic-native/in-app-browser";

@IonicPage({
  priority: "high",
})
@PageTrack({ pageName: "Home Page" })
@Component({
  selector: "page-home",
  templateUrl: "home.html",
})
export class HomePage {
  @ViewChild("slider") slides: Slides;
  @ViewChild(Content) content: Content;
  dir: string;
  layout: any;
  storeName: string = App.store;
  products: Array<any>;
  params: any = {};
  page: number = 1;
  hasMore: boolean = false;
  sortby: string = "date";
  per_page: number = 30;
  show_loader: boolean = true;
  showEmpty: boolean = false;
  filterData: any = {};
  items: Array<any>;
  categories: Array<any>;
  on_sale: boolean;
  featured: boolean;
  blog: Array<any>;

  constructor(
    public navCtrl: NavController,
    public navParams: NavParams,
    private iab: InAppBrowser,
    private WC: WooCommerceProvider,
    public loader: LoadingProvider,
    public wishlist: WishlistProvider,
    public modalCtrl: ModalController,
    public settings: SettingsProvider,
    private toast: ToastProvider,
    private splash: SplashScreen,
    public user: UserProvider,
    public platform: Platform,
    private admob: AdMobFree,
    public deeplinks: Deeplinks,
    private translate: TranslateService,
    private zone: NgZone
  ) {
    this.dir = platform.dir();
    this.WC.loadSetting();

    this.WC.getHomePageLayout().subscribe(
      (x) => {
        this.layout = x;
        this.settings.setSettings(x, "layout");
        // if (this.platform.is('cordova')) {
        // 	this.admob.interstitial.config({
        // 		id: 'ca-app-pub-2336008794991646/2315340216',
        // 		isTesting: false,
        // 		autoShow: true,
        // 		//size: 'SMART_BANNER'
        // 	});
        // 	this.admob.interstitial.prepare()
        // 		.then(() => {
        // 			// banner Ad is ready
        // 			// if we set autoShow to false, then we will need to call the show method here
        // 		});
        // }
      },
      (err) => {
        if (this.settings.layout) {
          this.layout = this.settings.layout;
        }
        console.log(err);
        toast.showError();
      }
    );
    if (this.platform.is("cordova")) {
      this.deeplinks.route({}).subscribe(
        (match) => {
          // match.$route - the route we matched, which is the matched entry from the arguments to route()
          // match.$args - the args passed in the link
          // match.$link - the full link data
          //console.log('Successfully matched route', match);
        },
        (nomatch) => {
          console.log("check deeplink");
          console.log(nomatch.$link.url);
          if (
            (nomatch.$link.url &&
              nomatch.$link.url.includes(App.url + "/product/")) ||
            (nomatch.$link.url &&
              nomatch.$link.url.includes(App.url + "/book/"))
          ) {
            this.loader.show();
            this.WC.getProductByUrl(nomatch.$link.url).subscribe(
              (res) => {
                this.loader.dismiss();
                if (res) {
                  this.goTo("ProductdetailPage", res);
                }
              },
              (err) => {
                this.loader.dismiss();
              }
            );
            console.log("go to product page");
          }
          // nomatch.$link - the full link data
          //console.error('Got a deeplink that didn\'t match', nomatch);
        }
      );
    }

    this.translate.get(["CATEGORIES", "PRICE"]).subscribe((x) => {
      this.items = [
        {
          name: x.PRICE,
          slug: "native_price",
          min_price: this.settings.appSettings.price.min || 0,
          max_price: this.settings.appSettings.price.max || "",
        },
        { name: x.CATEGORIES, slug: "product_cat" },
      ];
    });
    console.log(this.navParams);
    this.items[1].id = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.id
      : "";
    this.items[1].cat_name = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.name
      : "";
    this.params.search = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.search_data
      : "";
    this.sortby = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.sortby
      : "date";
    this.on_sale = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.on_sale
      : false;
    this.featured = this.navParams.data.hasOwnProperty("params")
      ? this.navParams.data.params.featured
      : false;
    console.log(this.params);
    console.log(this.items);
    this.loadProducts();
  }
  ionViewDidLoad() {
    console.log("ionViewDidLoad HomePage");
    this.splash.hide();
  }
  ionViewDidEnter() {
    if (this.slides !== undefined) {
      this.slides.resize();
      this.slides.update();
      this.slides.autoplayDisableOnInteraction = false;
      this.slides.startAutoplay();
    }
  }
  ionViewWillLeave() {
    if (this.slides !== undefined) {
      this.slides.stopAutoplay();
    }
  }

  setFav(product: any) {
    this.wishlist.add(product);
  }

  showSearch() {
    let modal = this.modalCtrl.create("SearchPage", {});
    modal.onDidDismiss((data) => {
      if (data && data.page) {
        this.goTo(data.page, data.params);
      }
    });
    modal.present();
  }
  handleSlideClick() {
    //Using This Function due to there is a bug in ion-slides in autoplay loop can't detect 1st silde click
    //Need To fix when official team fix this
    let index = this.slides._slides[this.slides.clickedIndex].getAttribute(
      "data-swiper-slide-index"
    );
    console.log(index);
    if (this.layout.banner[index].ref_banner_url === "") {
      this.goTo("ProductPage", this.layout.banner[index]);
    } else {
      this.loader.show();
      let browser = this.iab.create(
        this.layout.banner[index].ref_banner_url,
        "_blank",
        {
          location: "no",
          clearcache: "yes",
          clearsessioncache: "yes",
        }
      );
      this.loader.dismiss();
    }
  }
  handleSlideClickSeconBanner() {
    let index = this.slides._slides[this.slides.clickedIndex].getAttribute(
      "data-swiper-slide-index"
    );
    console.log(index);
    if (this.layout.second_banner[index].second_banner_url === "") {
      this.goTo("ProductPage", this.layout.second_banner[index]);
    } else {
      let browser = this.iab.create(
        this.layout.second_banner[index].second_banner_url,
        "_blank",
        {
          location: "no",
          clearcache: "yes",
          clearsessioncache: "yes",
        }
      );
    }
  }
  handleSlideClickThirdBanner() {
    let index = this.slides._slides[this.slides.clickedIndex].getAttribute(
      "data-swiper-slide-index"
    );
    console.log(index);
    if (this.layout.third_banner[index].third_banner_url === "") {
      this.goTo("ProductPage", this.layout.third_banner[index]);
    } else {
      let browser = this.iab.create(
        this.layout.third_banner[index].third_banner_url,
        "_blank",
        {
          location: "no",
          clearcache: "yes",
          clearsessioncache: "yes",
        }
      );
    }
  }
  loadProducts() {
    this.show_loader = true;
    this.products = [];
    this.WC.getCustomProducts(
      this.page,
      this.per_page,
      this.sortby,
      this.items[1].id,
      this.params.search,
      this.items[0].min_price,
      this.items[0].max_price,
      this.on_sale,
      this.featured,
      this.filterData
    ).subscribe(
      (res: any) => {
        this.show_loader = false;
        console.log(res);
        this.zone.run(() => {
          this.products = res;
          this.hasMore = res.length == this.per_page;
          this.showEmpty = res.length == 0;
          let p = this.joinProductIds(this.products);
          this.WC.getCustomAttributes(p).subscribe((res: any) => {
            res.forEach((element) => {
              element.active = false;
            });
            let newFilter = [];
            for (let i = 0; i < res.length; i++) {
              let flag = 0;
              for (let j = 2; j < this.items.length; j++) {
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
            this.items.length = 2;
            this.items = this.items.concat(newFilter);
            console.log(this.items);
          });
        });
      },
      (err) => {
        this.toast.showError();
        this.show_loader = false;
        console.log(err);
      }
    );
  }
  loadMoreProducts(event) {
    this.page++;
    this.WC.getCustomProducts(
      this.page,
      this.per_page,
      this.sortby,
      this.items[1].id,
      this.params.search,
      this.items[0].min_price,
      this.items[0].max_price,
      this.on_sale,
      this.featured,
      this.filterData
    ).subscribe((res: any) => {
      console.log(res);
      this.zone.run(
        () => {
          this.products = this.products.concat(res);

          let p = this.joinProductIds(this.products);
          this.WC.getCustomAttributes(p).subscribe((res: any) => {
            res.forEach((element) => {
              element.active = false;
            });
            this.items.length = 2;
            this.items = this.items.concat(res);
            console.log(this.items);
          });

          event.complete();
          if (res.length == this.per_page) {
            this.hasMore = true;
          } else {
            this.hasMore = false;
            event.enable(false);
          }
        },
        (err) => {
          this.toast.showError();
          console.log(err);
        }
      );
    });
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
  goTo(page, params) {
    this.navCtrl.push(page, { params: params }, { animate: false });
  }
  goToBlock(page, params) {
    console.log(params);
    if (params.block_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.block_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goToSecondBannerProduct(page, params) {
    console.log(params);
    if (params.banner2_product_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.banner2_product_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goToThirdBannerIcon(page, params) {
    console.log(params);
    if (params.banner3_icon_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.banner3_icon_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goToSingleBannerData(page, params) {
    console.log(params);
    if (params.single_banner_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.single_banner_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goTobanner_10x_first(page, params) {
    console.log(params);
    if (params.banner_10x_first_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.banner_10x_first_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goTonew_arrival(page, params) {
    console.log(params);
    if (params.new_arrival_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.new_arrival_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goTobanner_10x_second(page, params) {
    console.log(params);
    if (params.banner_10x_second_url === "") {
      this.navCtrl.push(page, { params: params }, { animate: false });
    } else {
      let browser = this.iab.create(params.banner_10x_second_url, "_blank", {
        location: "no",
        clearcache: "yes",
        clearsessioncache: "yes",
      });
    }
  }
  goToBlog(blog) {
    //this.loader.show();
    console.log(blog);
    let browser = this.iab.create(blog, "_blank", {
      location: "no",
      clearcache: "yes",
      clearsessioncache: "yes",
    });
    // this.loader.dismiss();
  }
}
