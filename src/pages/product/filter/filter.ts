import { SettingsProvider } from './../../../providers/settings/settings';
import { WooCommerceProvider } from './../../../providers/woocommerce/woocommerce';
import { FormGroup, FormBuilder } from '@angular/forms';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, ViewController, Platform } from 'ionic-angular';

@IonicPage({
  priority: 'high'
})
@Component({
  selector: 'page-filter',
  templateUrl: 'filter.html',
})
export class FilterPage {
  items: Array<any>;
  priceForm: FormGroup;
  categories: Array<any> = [];
  dir: string;

  constructor(public navCtrl: NavController, public navParams: NavParams, private zone: NgZone, private settings: SettingsProvider,
    private fb: FormBuilder, private viewCtrl: ViewController, private WC: WooCommerceProvider, platform: Platform) {
    this.dir = platform.dir();
    this.items = navParams.data.data;
    this.toggleClass(this.items[navParams.data.active])
    console.log(this.navParams.data.active);
    this.priceForm = this.fb.group({
      min_price: [this.items[0].min_price || 0],
      max_price: [this.items[0].max_price || '']
    });
    if (this.settings.category && this.settings.category.length > 0) {
      this.zone.run(() => {
        this.categories = this.settings.category;
      });
    } else {
      this.loadCategories();
    }
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad FilterPage');
  }

  loadCategories() {
    this.WC.getAllCategories().subscribe((res: any) => {
      this.settings.setSettings(res, "category");
      this.zone.run(() => {
        this.categories = res;
      })
    }, err => {
      console.log(err);
    });
  }

  toggleClass(item) {
    for (let i = 0; i < this.items.length; i++) {
      if (item.slug === this.items[i].slug) {
        this.items[i].active = true;
      } else {
        this.items[i].active = false;
      }
    }
  }

  close() {
    this.viewCtrl.dismiss(null);
  }
  submit() {
    this.items[0].min_price = this.priceForm.value.min_price;
    this.items[0].max_price = this.priceForm.value.max_price;
    this.viewCtrl.dismiss(this.items);
  }
  count(item) {
    let i = 0;
    for (let j in item.options) {
      if (item.options[j].checked) {
        i++
      }
    }
    return i > 0 ? " (" + i + ")" : '';
  }
  setName() {
    for (let i in this.categories) {
      if (this.categories[i].id == this.items[1].id) {
        this.items[1].cat_name = this.categories[i].name;
      }
    }
  }
  reset() {
    this.priceForm.controls['min_price'].setValue("0");
    this.priceForm.controls['max_price'].setValue("");
    this.items[1].cat_name = '';
    this.items[1].id = '';
    for (let i = 2; i < this.items.length; i++) {
      for (let j in this.items[i].options) {
        this.items[i].options[j].checked = false;
      }
    }
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
}
