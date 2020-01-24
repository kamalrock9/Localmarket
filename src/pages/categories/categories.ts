import { WishlistProvider, WooCommerceProvider, SettingsProvider } from './../../providers/providers';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, ModalController } from 'ionic-angular';
//import * as _ from 'lodash';

@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-categories',
  templateUrl: 'categories.html',
})
export class CategoriesPage {
  categories: Array<any>;
  nodes: Array<any>;
  options = { displayField: 'name' };
  page = 1;
  tmp: Array<any> = [];
  constructor(public navCtrl: NavController, public navParams: NavParams, public settings: SettingsProvider,private modalCtrl:ModalController,
    private WC: WooCommerceProvider, public wishlist: WishlistProvider, private zone: NgZone) {


  }
  getAllCategories() {
    if (this.settings.category && this.settings.category.length > 0) {
      this.categories = this.convert(this.settings.category);
      this.zone.run(() => {
        this.nodes = this.categories;
      });
    } 
      this.WC.getAllCategories().subscribe((res) => {
        this.settings.setSettings(res, "category");
        this.categories = this.convert(res);
        this.zone.run(() => {
          this.nodes = this.categories;
        });
      });
    
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad CategoriesPage');
    this.getAllCategories();
  }

  convert(dataList) {
    Object.keys(dataList).forEach((key) => {
      dataList[key].isExpanded = false;
    });
    var tree = [],
      mappedArr = {},
      arrElem,
      mappedElem;
    // First map the nodes of the array to an object -> create a hash table.
    for (var i = 0, len = dataList.length; i < len; i++) {
      arrElem = dataList[i];
      mappedArr[arrElem.id] = arrElem;
      mappedArr[arrElem.id]['children'] = [];
    }
    for (var id in mappedArr) {
      if (mappedArr.hasOwnProperty(id)) {
        mappedElem = mappedArr[id];
        // If the element is not at the root level, add it to its parent array of children.
        if (mappedElem.parent != 0) {
          mappedArr[mappedElem['parent']]['children'].push(mappedElem);
        }
        // If the element is at the root level, add it to first level elements array.
        else {
          tree.push(mappedElem);
        }
      }
    }

    return tree;
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
  goTo(page, params) {
    this.navCtrl.push(page, { params: params }, { animate: false });
  }
}
