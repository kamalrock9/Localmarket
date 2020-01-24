import { ToastProvider } from './../../../providers/toast/toast';
import { LoadingProvider } from './../../../providers/loading/loading';
import { SettingsProvider, WooCommerceProvider } from './../../../providers/providers';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, AlertController } from 'ionic-angular';

@IonicPage({
    priority: 'low'
})
@Component({
    selector: 'page-order-detail',
    templateUrl: 'detail.html',
})
export class OrderDetailPage {

    order: string = "detail";
    data: any;

    constructor(public nav: NavController, private params: NavParams, public settings: SettingsProvider, private toast: ToastProvider,
        private WC: WooCommerceProvider, private zone: NgZone, private alertCtrl: AlertController, private loader: LoadingProvider) {
        this.data = this.params.data.params;
        console.log(this.params.data.params);
        this.data.line_items.forEach(element => {
            //console.log(element.id); 
            this.WC.getProductThumb(element.product_id).subscribe((res: any) => {
                this.zone.run(() => {
                    element.img_src = res.src;
                })
            }, err => {
                console.log(err);
            })
        });
    }

    ionViewDidLoad() {
        console.log('ionViewDidLoad OrderDetailPage');
    }
    calculatePrice(x) {
        return (x.prices_include_tax ? x.total : (Number(x.total) + Number(x.total_tax)).toFixed(2));
    }
    orderCancel() {
        let confirm = this.alertCtrl.create({
            title: "Cancel Order",
            message: "Are you sure you want to cancel this order",
            buttons: [{
                text: "No"
            }, {
                text: "Yes",
                handler: () => {
                    this.loader.show();
                    let x = {
                        status: 'cancelled'
                    }
                    this.WC.updateOrder(this.data.id, x).then((res => {
                        this.loader.dismiss();
                        this.data.status = res.status;
                    }), err => {
                        this.loader.dismiss();
                        this.toast.showError();
                    });
                }
            }]
        });
        confirm.present();
    }
}
