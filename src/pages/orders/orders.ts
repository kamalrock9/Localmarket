import { WooCommerceProvider, UserProvider, SettingsProvider, ToastProvider } from './../../providers/providers';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController } from 'ionic-angular';


@IonicPage({
  priority: 'low'
})
@Component({
  selector: 'page-orders',
  templateUrl: 'orders.html',
})
export class OrdersPage {
  orders: Array<any>;
  per_page: number = 20;
  page: number = 1;
  hasMore: boolean = false;

  constructor(public navCtrl: NavController, public navParams: NavParams, private WC: WooCommerceProvider,private modalCtrl:ModalController,
    private toast: ToastProvider, public user: UserProvider, public settings: SettingsProvider, private zone: NgZone) {

    this.getOrders();
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad OrdersPage');
  }
  
  getOrders() {
    if (this.user.all) {
      this.WC.getOrders(this.user.id, this.page, this.per_page).then(data => {
        console.log(data);
        this.zone.run(() => {
          this.orders = data;
          this.hasMore = (data.length == this.per_page);
        })
      }, err => {
        this.toast.showError();
      });
    }
  }
  loadMoreOrders(event) {
    this.page++;
    this.WC.getOrders(this.user.id, this.page, this.per_page).then((data) => {
      this.zone.run(() => {
        this.orders = this.orders.concat(data);
        event.complete();
        if (data.length == this.per_page) {
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
  showSearch() {
		let modal = this.modalCtrl.create('SearchPage', {});
		modal.onDidDismiss((data) => {
			if (data && data.page) {
				this.goTo(data.page, data.params);
			}
		});
		modal.present();
	} 
  goHome() {
    this.navCtrl.popToRoot();
  }
  goTo(page, params) {
		this.navCtrl.push(page, { params: params }, { animate: false });
	}

  calculatePrice(x) {
    return (x.prices_include_tax ? x.total : (Number(x.total) + Number(x.total_tax)).toFixed(2));
  }
}
