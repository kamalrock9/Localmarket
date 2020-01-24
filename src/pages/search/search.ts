import { WooCommerceProvider } from './../../providers/woocommerce/woocommerce';
import { Component, ViewChild, NgZone } from '@angular/core';
import { IonicPage, App, ViewController, NavController, Searchbar } from 'ionic-angular';
import { SettingsProvider } from '../../providers/providers';

@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-search',
  templateUrl: 'search.html',
})

export class SearchPage {
  @ViewChild('searchBar') searchbar: Searchbar;
  search: string = '';
  products: Array<any>;
  results: any;
  constructor(public settings: SettingsProvider, public appCtrl: App, public viewCtrl: ViewController, private zone: NgZone,
    public navCtrl: NavController, private WC: WooCommerceProvider) {
  }

  submit(page, params) {
    this.viewCtrl.dismiss({ page: page, params: params });
  }
  searchSuggestions() {
    if (this.search == '') {
      this.results = [];
    } else {
      this.WC.search(this.search, 4).subscribe(res => {
        this.zone.run(() => {
          this.results = res;
        });
      }, err => {
        console.log(err);
      });
    }
  }

  ionViewDidEnter() {

  }

  ionViewWillEnter() {
    //this.history.load();
  }
  dismiss() {
    this.viewCtrl.dismiss();
  }
}
