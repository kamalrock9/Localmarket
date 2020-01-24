import { NotifProvider } from './../../providers/notif/notif';
import { Component } from '@angular/core';
import { IonicPage, AlertController, NavController } from 'ionic-angular';

@IonicPage({
  priority: 'off'
})
@Component({
  selector: 'page-notification',
  templateUrl: 'notification.html',
})
export class NotificationPage {
  notif: any[] = [];

  constructor(private _notif: NotifProvider, private alert: AlertController, public navCtrl: NavController) {
    console.log(_notif.all);
  }

  showAlert(x: any) {
    this.alert.create({
      title: x.title,
      subTitle: x.body,
      buttons: ['OK']
    }).present();
  }

  remove(x: any) {
    this._notif.remove(x.id);
  }
  // clear(){
  //   this.notif=[]; 
  //   this._notif.clear;
  // }

  ionViewDidLoad() {
    this.notif = this._notif.all.reverse();
    console.log('ionViewDidLoad AccountNotificationPage');
  }
  openNotification(x) {
    if (x.additionalData) {
      if (x.additionalData.product_id) {
        let params = {
          id: x.additionalData.product_id,
          isReferedByPush: true
        }
        this.goTo('ProductdetailPage', params);

      } else if (x.additionalData.category_id) {
        let params = {
          id: x.additionalData.category_id
        }
        this.goTo('ProductPage', params);
      } else if (x.additionalData.brand_id) {
        let params = {
          brand_id: x.additionalData.brand_id
        }
        this.goTo('ProductPage', params);
      } else {
        this.showAlert(x);
      }
    } else {
      this.showAlert(x);
    }
  }
  goTo(page, params) {
    this.navCtrl.push(page, { params: params }, { animate: false });
  }

}
