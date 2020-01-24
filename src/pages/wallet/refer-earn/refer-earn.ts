import { AppVersion } from '@ionic-native/app-version';
import { SocialSharing } from '@ionic-native/social-sharing';
import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ViewController, Platform } from 'ionic-angular';
import { App } from '../../../app/app.config';
import { UserProvider, ToastProvider } from '../../../providers/providers';

@IonicPage({
  priority: 'low'
})
@Component({
  selector: 'page-refer-earn',
  templateUrl: 'refer-earn.html',
})
export class ReferEarnPage {
  storeName: string = App.store;
  referralData: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, private viewCtrl: ViewController, private toast: ToastProvider,
    private socialSharing: SocialSharing, private user: UserProvider, private appVersion: AppVersion, private platform: Platform) {
    this.user.getReferEarnData().subscribe((x) => {
      console.log(x);
      this.referralData = x;
    }, (err) => {
      console.log(err);
    });

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad ReferEarnPage');
  }
  dismiss() {
    this.viewCtrl.dismiss();
  }
  share() {
    if (this.platform.is('cordova')) {
      let msg = this.referralData.message;
      if (this.platform.is('ios')) {
        let url = "https://itunes.apple.com/app/id" + App.IosAppId;
        this.socialSharing.share(msg, this.storeName, '', url).then((x) => {
          console.log(x);
        }).catch((err) => {
          console.log(err);
        });
      }
      if (this.platform.is('android')) {
        this.appVersion.getPackageName().then(res => {
          let url = "https://play.google.com/store/apps/details?id=" + res;
          this.socialSharing.share(msg, this.storeName, '', url).then((x) => {
            console.log(x);
          }).catch((err) => {
            console.log(err);
          });
        });
      }
    } else {
      this.toast.show("Device Only");
     }
  }
}
