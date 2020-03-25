import { NotifProvider } from './../../providers/notif/notif';
import { Component } from '@angular/core';
import { IonicPage, AlertController } from 'ionic-angular';

@IonicPage({
  priority:'off'
})
@Component({
  selector: 'page-notification',
  templateUrl: 'notification.html',
})
export class NotificationPage {
  notif: any[] = [];
  
  constructor(private _notif: NotifProvider, private alert: AlertController) {
    console.log(_notif.all);
  }

  showAlert(x: any) {
    this.alert.create({
      title: x.title,
      subTitle: x.body,
      buttons: ['OK']
    }).present();
  }

  remove(x: any){
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

}
