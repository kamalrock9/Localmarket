import { AppVersion } from '@ionic-native/app-version';
import { UserProvider } from './../../providers/providers';
import { Component } from '@angular/core';
import { IonicPage, NavController, Platform } from 'ionic-angular';

@IonicPage({
  priority:'off'
})
@Component({
  selector: 'page-app-setting',
  templateUrl: 'app-setting.html',
})
export class AppSettingPage {
  app: any={};

  constructor(public navCtrl: NavController, private platform: Platform, private appVersion: AppVersion,
     public user: UserProvider) {
    if (this.platform.is('cordova')) {
      this.appVersion.getVersionNumber().then(res => {
        this.app.version = res;
      })
    }
  }
  goTo(page, params) {
    this.navCtrl.push(page, { params: params });
  }
}
