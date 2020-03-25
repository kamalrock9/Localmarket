import { HTTP } from '@ionic-native/http';
import { UserProvider } from './../user/user';
import { Injectable } from '@angular/core';
import { App } from '../../app/app.config';


@Injectable()
export class RestProvider {
    url: string;
    isApp = true;

    constructor(private user: UserProvider, private ahttp: HTTP) {
        this.url = App.url + "/wp-json/wc/v2";
        this.ahttp.setHeader("*", 'Content-Type', 'application/json');
    }
    addToCart(data) {
        this.ahttp.setDataSerializer('json');

        return this.ahttp.post(this.url + "/cart/add", data, {});
    }

    getCart(pakage, shipping_method?: string) {
        let country = pakage.country;
        let state = pakage.state;
        let postcode = pakage.postcode;

        let c = country ? "?country=" + country : "?";
        let s = state ? "&state=" + state : "";
        let p = postcode ? "&postcode=" + postcode : "";
        let sm = shipping_method ? "&shipping_method=" + shipping_method : "";
        let uid = this.user.id ? "&user_id=" + this.user.id : "";
        return this.ahttp.get(this.url + "/cart" + c + s + p + sm + uid, {}, {});
    }
    removeCartItem(pakage, cart_item_key: string) {
        let country = pakage.country;
        let state = pakage.state;
        let postcode = pakage.postcode;

        let c = country ? "?country=" + country : "?";
        let s = state ? "&state=" + state : "";
        let p = postcode ? "&postcode=" + postcode : "";
        let cik = cart_item_key ? "&cart_item_key=" + cart_item_key : "";
        return this.ahttp.get(this.url + "/cart/remove" + c + s + p + cik, {}, {});
    }
    applyCoupon(pakage, coupon_code: string) {
        let country = pakage.country;
        let state = pakage.state;
        let postcode = pakage.postcode;

        let c = country ? "?country=" + country : "?";
        let s = state ? "&state=" + state : "";
        let p = postcode ? "&postcode=" + postcode : "";
        let cc = coupon_code ? "&coupon_code=" + coupon_code : "";
        let uid = this.user.id ? "&user_id=" + this.user.id : "";
        return this.ahttp.get(this.url + "/cart/coupon" + c + s + p + cc + uid, {}, {});
    }
    removeCoupon(pakage, coupon_code: string) {
        let country = pakage.country;
        let state = pakage.state;
        let postcode = pakage.postcode;

        let c = country ? "?country=" + country : "?";
        let s = state ? "&state=" + state : "";
        let p = postcode ? "&postcode=" + postcode : "";
        let cc = coupon_code ? "&coupon_code=" + coupon_code : "";
        return this.ahttp.get(this.url + "/cart/remove-coupon" + c + s + p + cc, {}, {});
    }

    updateCart(pakage, cart_item_key: string, quantity: number) {
        let country = pakage.country;
        let state = pakage.state;
        let postcode = pakage.postcode;
        let c = country ? "?country=" + country : "?";
        let s = state ? "&state=" + state : "";
        let p = postcode ? "&postcode=" + postcode : "";
        let cik = cart_item_key ? "&cart_item_key=" + cart_item_key : "";
        let q = quantity ? "&quantity=" + quantity : "";
        return this.ahttp.get(this.url + "/cart/update" + c + s + p + cik + q, {}, {});
    }

    getCartCount() {
        return this.ahttp.get(this.url + "/cart/item-count", {}, {});
    }
    getReview(shipping_method?: string, chosen_payment_method?: string, pay_via_wallet?: boolean) {
        if (this.user && this.user.shipping) {
            let c = "?country=" + this.user.shipping.country;
            let st = "&state=" + this.user.shipping.state;
            let p = "&postcode=" + this.user.shipping.postcode;
            let s = shipping_method ? "&shipping_method=" + shipping_method : "";
            let uid = this.user.id ? "&user_id=" + this.user.id : "";
            let cp = chosen_payment_method ? "&chosen_payment_method=" + chosen_payment_method : "";
            let pw = pay_via_wallet ? "&pay_via_wallet=" + pay_via_wallet : "";
            return this.ahttp.get(this.url + "/checkout/review-order" + c + st + p + s + cp + uid + pw, {}, {});
        } else {
            let s = shipping_method ? "?shipping_method=" + shipping_method : "?";
            let cp = chosen_payment_method ? "&chosen_payment_method=" + chosen_payment_method : "";
            return this.ahttp.get(this.url + "/checkout/review-order" + s + cp, {}, {});

        }
    }
    createOrder(payment_method, shipping_method, data = {}, pay_via_wallet?) {
        this.ahttp.setDataSerializer('json');

        console.log(data);
        let u = (this.user && this.user.id) ? "?user_id=" + this.user.id : "?user_id=";
        let p = "&payment_method=" + payment_method;
        let c = "&shipping_method=" + shipping_method;
        let pw = pay_via_wallet ? "&pay_via_wallet=" + pay_via_wallet : "";
        console.log(u + p + c + pw);
        return this.ahttp.post(this.url + "/checkout/new-order" + u + p + c + pw, data, {});
    }
    getCartClear() {
        return this.ahttp.get(this.url + "/cart/clear", {}, {}).then((res) => {
            this.ahttp.clearCookies();
        }).catch(err => {
            this.ahttp.clearCookies();
        });
    }
    getWalletDetails() {
        return this.ahttp.get(this.url + "/wallet?uid=" + this.user.id, {}, {});
    }
    addMoneyToCart(amount) {
        this.ahttp.setDataSerializer('json');

        let data = {
            "woo_add_to_wallet": "Add",
            "woo_wallet_balance_to_add": amount
        }
        return this.ahttp.post(this.url + "/wallet/add", data, {});
    }


} 
