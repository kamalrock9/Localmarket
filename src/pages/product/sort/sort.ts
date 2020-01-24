import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams, ViewController } from 'ionic-angular';

@IonicPage({
  priority:'low'
})
@Component({
  selector: 'page-sort',
  templateUrl: 'sort.html',
})
export class SortPage {
  sortby:string;
  firstTime:boolean=true;
  constructor(public navCtrl: NavController, public navParams: NavParams,private viewCtrl:ViewController) {
    this.sortby=this.navParams.data.params;
  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad SortPage');
  }
  dismiss(data?) {
    if(data && this.firstTime){
      this.firstTime=!this.firstTime;
      return;
    }
    this.viewCtrl.dismiss(data);
  }
}
