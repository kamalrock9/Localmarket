import { TranslateService } from '@ngx-translate/core';
import { SettingsProvider } from './../../providers/settings/settings';
import { ToastProvider, RestProvider, LoadingProvider } from './../../providers/providers';
import { Component, ViewChild, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, Content, AlertController } from 'ionic-angular';
import { Events } from 'ionic-angular/util/events';


@IonicPage({
  priority: 'off'
})
@Component({
  selector: 'page-wallet',
  templateUrl: 'wallet.html',
})
export class WalletPage {
  @ViewChild(Content) content: Content;
  wallet: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, public settings: SettingsProvider, private rest: RestProvider,
    private toast: ToastProvider, zone: NgZone, private alertCtrl: AlertController, private events: Events, private loader: LoadingProvider,
    private translate : TranslateService) {
    this.rest.getWalletDetails().then((data) => {
      console.log(data);
      zone.run(() => {
        this.wallet = JSON.parse(data.data);
      })
    }).catch(err => {
      toast.showError();
    });
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad WalletPage');
  }

  isArray(transaction: any) {
    return (transaction instanceof Array);
  }

  addMoney() {
    this.translate.get(['ADD_MONEY_TO_WALLET','AMOUNT','CANCEL','OK']).subscribe(x=>{
      let alert = this.alertCtrl.create({
        title: x.ADD_MONEY_TO_WALLET,
        inputs: [
          {
            name: 'amount',
            placeholder: x.AMOUNT,
            type: 'number'
          }
        ],
        buttons: [
          {
            text: x.CANCEL,
            role: 'cancel'
          },
          {
            text: x.OK,
            handler: data => {
              this.loader.show();
              this.rest.addMoneyToCart(data.amount).then((data: any) => {
                console.log(data);
                this.loader.dismiss();
                let res = JSON.parse(data.data);
                this.events.publish("cartchanged");
                if (res.code) {
                  this.goTo('CheckoutPage', {});
                }
                this.toast.show(res.message);
              }).catch(err => {
                this.loader.dismiss();
                this.toast.showError();
                console.log(err);
              });
            }
          }
        ]
      });
      alert.present();
    });
    
  }
  goTo(page, params) {
    this.navCtrl.push(page, { params: params });
  }
}
