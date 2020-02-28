import { SettingsProvider } from "./../settings/settings";
import { Injectable } from "@angular/core";
import { Storage } from "@ionic/storage";
import * as WC from "woocommerce-api";
import { App } from "../../app/app.config";
import { HttpClient, HttpHeaders } from "@angular/common/http";
import "rxjs/add/operator/map";

@Injectable()
export class WooCommerceProvider {
  WooCommerce: any;
  woo: any = {};
  RestURL: string;
  constructor(
    public storage: Storage,
    public http: HttpClient,
    public temp: SettingsProvider
  ) {
    this.RestURL = App.url + "/wp-json/wc/v2/";
    this.init();
  }

  init() {
    this.WooCommerce = WC({
      url: App.url,
      consumerKey: App.consumerKey,
      consumerSecret: App.consumerSecret,
      wpAPI: true, //or false if you want to use the legacy API v3
      version: "wc/v2", //or wc/v1
      //verifySsl: true,
      queryStringAuth: true
    });
    return this.WooCommerce;
  }

  // getAllCategories(parent?, per_page = 100, order?, orderby?) {//: Promise<any> {
  //   let pt = parent ? "&parent=" + parent : '';
  //   let pp = "?per_page=" + per_page;
  //   let o = order ? "&order=" + order : '';
  //   let ob = orderby ? "&orderby=" + orderby : '';
  //   return this.WooCommerce.getAsync("products/categories" + pp + pt + o + ob).then((x) => {
  //     console.log(JSON.parse(x.body));
  //     return JSON.parse(x.body);
  //   });
  // }
  getAllCategories(parent?) {
    //: Promise<any> {
    let pt = parent ? "?parent=" + parent : "";
    return this.http.get(this.RestURL + "products/all-categories" + pt);
  }
  getSubCategories(c: any) {
    //: Promise<any> {
    return this.WooCommerce.getAsync("products/categories?parent=" + c).then(
      x => {
        console.log(JSON.parse(x.body));
        return JSON.parse(x.body);
      }
    );
  }

  loadSetting() {
    return this.WooCommerce.getAsync("system_status").then(
      x => {
        this.temp.setSettings(JSON.parse(x.body).settings, "settings");
        return JSON.parse(x.body).settings;
      },
      err => {
        console.log(err);
      }
    );
  }

  // getAllProducts(page: number, category: number, search: string, min_price: number, max_price: number, per_page: number, status: string, order: string, attribute: string = '', attribute_term: string = '', include: string, orderby: string, featured: boolean, on_sale: boolean, visible: boolean) {//: Promise<any> {
  //   let p = page ? '&page=' + page : '';
  //   let pp = per_page ? '&per_page=' + per_page : '&per_page=10';
  //   let c = category ? '&category=' + category : '';
  //   let q = search ? '&search=' + search : '';
  //   let min = min_price ? '&min_price=' + min_price : '';
  //   let max = max_price ? '&max_price=' + max_price : '';
  //   let s = status ? '?status=' + status : '?status=publish';
  //   let a = attribute ? '&attribute=' + attribute : '';
  //   let t = attribute_term ? '&attribute_term=' + attribute_term : '';
  //   let o = order ? '&order=' + order : '';
  //   let i = include ? '&include=' + include : '';
  //   let ob = orderby ? '&orderby=' + orderby : '';
  //   let f = featured ? '&featured=' + featured : '';
  //   let os = on_sale ? '&on_sale=' + on_sale : '';
  //   let v = visible ? '&visible=' + visible : '';
  //   return this.WooCommerce.getAsync("products" + s + p + pp + c + q + min + max + a + t + o + i + ob + f + os + v).then((x) => {
  //     console.log(JSON.parse(x.body));
  //     return JSON.parse(x.body);
  //   });
  // }
  getProductById(include?, id?) {
    //Multiple ids suppoerted(include=1,2,3,4,5) or single id supported(id=1)
    let incl = include ? "?include=" + include : "";
    let i = id ? "?id=" + id : "";
    return this.http.get(this.RestURL + "get-products-by-id" + incl + i);
  }
  search(search, per_page?) {
    let s = search ? "?search=" + search : "?search=''";
    let pp = per_page ? "&per_page=" + per_page : "";
    return this.http.get(this.RestURL + "custom-search" + s + pp);
  }
  getCustomProducts(
    page,
    per_page,
    sort,
    category,
    brand,
    search,
    min_price?,
    max_price?,
    on_sale?,
    featured?,
    filterData?
  ) {
    let p = page ? "?page=" + page : "?page=1";
    let pp = per_page ? "&per_page=" + per_page : "&per_page=10";
    let s = sort ? "&sort=" + sort : "&sort=default";
    let b = brand ? "&brand=" + brand : "";
    let c = category ? "&category=" + category : "";
    let sh = search ? "&search=" + search : "";
    let min_p = min_price ? "&min_price=" + min_price : "";
    let max_p = max_price ? "&max_price=" + max_price : "";
    let os = on_sale ? "&on_sale=" + on_sale : "";
    let f = featured ? "&featured=" + featured : "";
    console.log("custom-products" + p + pp + s + c + sh + min_p + max_p);
    return this.http.post(
      this.RestURL +
        "custom-products" +
        p +
        pp +
        s +
        c +
        b +
        sh +
        min_p +
        max_p +
        os +
        f,
      filterData
    );
  }
  getProductThumb(id) {
    return this.http.get(this.RestURL + "product/thumbnail?id=" + id);
  }
  getAttributeTerms(id: number) {
    return this.WooCommerce.getAsync(
      "products/attributes/" + id + "/terms?hide_empty=true"
    ).then(x => {
      // console.log(JSON.parse(x.body));
      return JSON.parse(x.body);
    });
  }

  getAttributes() {
    //: Promise<any> {

    return this.WooCommerce.getAsync("products/attributes").then(x => {
      // console.log(JSON.parse(x.body));
      return JSON.parse(x.body);
    });
  }
  getProductAttributes(id?: number) {
    let i = id ? "?id=" + id : "";
    return this.WooCommerce.getAsync("products/product-attributes/" + i).then(
      x => {
        // console.log(JSON.parse(x.body));
        return JSON.parse(x.body);
      }
    );
  }

  getAllBrands() {
    return this.http.get(this.RestURL + "products/all-brands");
  }

  getCustomAttributes(product?) {
    let p = product ? "&product=" + product : "";
    return this.http.get(
      this.RestURL + "products/custom-attributes?hide_empty=true" + p
    );
  }

  getProductVariation(data) {
    //Custom api made by phoeniixx
    return this.http.post(this.RestURL + "products/get-variation", data);
  }
  getOrders(id: number, page: number, per_page: number) {
    let p = page ? "&page=" + page : "";
    let pp = per_page ? "&per_page=" + per_page : "&per_page=10";
    return this.WooCommerce.getAsync("orders?customer=" + id + p + pp).then(
      data => {
        return JSON.parse(data.body);
      }
    );
  }
  updateOrder(id, status, transaction_id?) {
    var data = {
      status: status,
      transaction_id: transaction_id || ""
    };
    return this.WooCommerce.putAsync("orders/" + id, data).then(data => {
      return JSON.parse(data.body);
    });
  }
  getOrder(order_id: number) {
    return this.WooCommerce.getAsync("orders/" + order_id).then(data => {
      return JSON.parse(data.body);
    });
  }
  getDownloads(id: number) {
    return this.WooCommerce.getAsync("customers/" + id + "/downloads").then(
      x => {
        // console.log(JSON.parse(x.body));
        return JSON.parse(x.body);
      }
    );
  }
  getUserInfo(id: number) {
    return this.WooCommerce.getAsync("customers/" + id).then(x => {
      // console.log(JSON.parse(x.body));
      return JSON.parse(x.body);
    });
  }
  updateUserInfo(id: number, data) {
    return this.WooCommerce.postAsync("customers/" + id, data).then(x => {
      return JSON.parse(x.body);
    });
  }
  createOrder(data) {
    return this.WooCommerce.postAsync("orders", data).then(x => {
      return JSON.parse(x.body);
    });
  }
  saveAppSettings() {
    return this.http.get(this.RestURL + "app-settings");
  }
  checkPincode(pincode, product_id?) {
    let p: any = {};
    p.pincode = pincode;
    if (product_id) {
      p.product_id = product_id;
    }
    return this.http.post(this.RestURL + "checkpincode", p);
  }

  getFAQ() {
    return this.http.get(this.RestURL + "faq-settings");
  }

  getCoupons() {
    return this.WooCommerce.getAsync("coupons").then(x => {
      return JSON.parse(x.body);
    });
  }
  getReviewSettings(product_id, user_id) {
    let p_id = product_id ? "?product_id=" + product_id : "?";
    let u_id = user_id ? "&user_id=" + user_id : "";

    return this.http
      .get(this.RestURL + "product/review-settings" + p_id + u_id)
      .subscribe(x => {
        this.temp.setSettings(x, "reviewSettings");
        return x;
      });
  }
  getProductReviews(id: number, status = "approved") {
    let WCv3 = WC({
      url: App.url,
      consumerKey: App.consumerKey,
      consumerSecret: App.consumerSecret,
      wpAPI: true, //or false if you want to use the legacy API v3
      version: "wc/v3", //or wc/v1
      //verifySsl: true,
      queryStringAuth: true
    });
    let p = id ? "?product=" + id : "";
    let s = "&status=" + status;
    return WCv3.getAsync("products/reviews" + p + s).then(x => {
      console.log(JSON.parse(x.body));
      return JSON.parse(x.body);
    });
  }
  postProductReviews(data) {
    let WCv3 = WC({
      url: App.url,
      consumerKey: App.consumerKey,
      consumerSecret: App.consumerSecret,
      wpAPI: true, //or false if you want to use the legacy API v3
      version: "wc/v3", //or wc/v1
      //verifySsl: true,
      queryStringAuth: true
    });

    return WCv3.postAsync("products/reviews", data).then(x => {
      return JSON.parse(x.body);
    });
  }
  getTermConditon() {
    return this.http.get(this.RestURL + "terms");
  }
  getHomePageLayout() {
    return this.http.get(this.RestURL + "layout");
  }
  getProductByUrl(url) {
    return this.http.get(this.RestURL + "get-product-by-url?url=" + url);
  }

  generateChecksumAPI(data) {
    // const httpOptions = {
    //   headers: new HttpHeaders({
    //     'Content-Type': 'application/x-www-form-urlencoded'
    //   })
    // };

    // let urlParams = Object.keys(data).map(function (k) {
    //   return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    // }).join('&');

    //return this.http.post(this.RestURL + "checkout/paytm-checksum", urlParams, httpOptions);

    let formData: FormData = new FormData();

    Object.keys(data).forEach(k => {
      formData.append(k, data[k]);
    });

    return this.http.post(this.RestURL + "checkout/paytm-checksum", formData);
  }
}
