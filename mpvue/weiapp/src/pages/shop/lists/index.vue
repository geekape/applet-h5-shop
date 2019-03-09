<template>
  <div class="lists">
    <div class="g-flex">
      <search :type="2"></search>
      <span class="icon-filter" @click="togglePopup"></span>
    </div>
    <goodsList v-if='goods[0]' :goodsData='goods'></goodsList>

    <!-- 没有商品 -->
    <div class="hint-page" v-else>
      <img lazy-load :src="imgRoot+'nothing.png'" />
      <p class="hint-page__text">还没有任何商品</p>
    </div>
    <van-popup :show="isPopup" position="right" @close="togglePopup" class="popup">
      <form @submit="filterData">
        <div class="popup-item">
          <p class="popup-item__tt">价格区间(元)</p>
          <div class="popup-item__price">
            <input type="number" class="popup-item__search" @blur="setValue" data-index="1"><div class="popup-item__line">—</div><input type="number" class="popup-item__search" @blur="setValue" data-index="2">
          </div>
        </div>

        <div class="popup-item" v-for="(value,key) in sortList.top_list" v-if="sortList.sub_list[key]" :key="value.pid">
          <p class="popup-item__tt">{{value.title}}</p>

          <div class="popup-item__sort">
            <checkbox-group v-for="item in sortList.sub_list[key]" @click="toggleCheckbox(item.id)" :key="item.id">
              <input type="checkbox" :id="item.id" :value="item.id" v-model="checkSort">
              <label :for="item.id">{{item.title}}</label>
            </checkbox-group>
          </div>
          

        </div>

        <div class="popup-button">
          <button form-type="reset" class="popup-button__reset">重置</button>
          <button form-type="submit" class="popup-button__sure">确定</button>
        </div>
      </form>
    </van-popup>
  </div>
</template> 

<script>
import search from "@/components/shop/search";
import goodsList from "@/components/shop/goodsList";
import { post, get, host } from "@/utils";

export default {
    mpType: 'page',

  components: {
    goodsList,
    search
  },

  data() {
    return {
			imgRoot: this.imgRoot,
      datas: [],
      goods: [],
      isPopup: false,
      minPrice: 0,
      maxPrice: 0,
      sortList: [],
      checkSort: []
    };
  },
 
  methods: {
    toggleCheckbox(id) {
      let arr = this.checkSort
      arr.push(id)
      this.checkSort = [...new Set(arr)]
      console.log(this.checkSort);
    },
    checkboxChange(e) {
      console.log("checkbox发生change事件，携带value值为：", e.detail.value);
    },
    filterData(e) {
      console.log(e);
      const _this = this;
      let opt = {
        cate_id: _this.checkSort.join(",")
      };
      if (this.minPrice && this.maxPrice) {
        opt.min_price = this.minPrice;
        opt.max_price = this.maxPrice;
      }
      post("shop/api/lists", opt).then(data => {
        console.log(data);
        _this.goods = data.goods;
        
      });

      this.isPopup = false;
    },
    setValue(e) {
      console.log(e);
      let val = e.target.value;
      let index = e.target.dataset.index;
      console.log(e, val, index);
      if (index == 1) {
        this.minPrice = val || 0;
      } else {
        this.maxPrice = val || 0;
      }
    },
    // 发送请求
    getData(opt) {
      var _this = this;
      console.log(_this.GLOBAL.app.id, _this.GLOBAL.app.pid);
      post(host + "shop/api/lists", opt).then(data => {
        console.log(data);
        _this.goods = data.goods;

        // 清空值
        _this.GLOBAL.app.id = 0;
        _this.GLOBAL.app.pid = 0;
        _this.GLOBAL.app.listsType = 0;
      });
    },
    search() {
      var _this = this;
      console.log("search_key:" + _this.GLOBAL.app.searchKey);
      post("shop/api/lists", {
        search_key: _this.GLOBAL.app.searchKey
      }).then(data => {
        console.log(data);
        _this.goods = data.goods;

        // 清空值
        _this.GLOBAL.app.listsType = 0;
        _this.GLOBAL.app.listsTysearchKeype = "";
      });
    },
    togglePopup() {
      this.isPopup = !this.isPopup;
    }
  },
  onShow() {
    const _this = this;
    if (this.GLOBAL.app.listsType == 1) {
      // 搜索跳转
      console.log("搜索跳转");
      this.search();
    } else if (this.GLOBAL.app.listsType == 2) {
      // 同款跳转
      this.getData({
        tab_goods_id: _this.GLOBAL.app.id,
        tab: _this.GLOBAL.app.pid
      });
    } else {
      this.getData({
        cate_id: _this.GLOBAL.app.id,
        pid: _this.GLOBAL.app.pid
      });
    }
  },

  onLoad() {
    // 获取分类
    get("shop/api/category").then(res => {
      this.sortList = res;
    });
  }
};
</script>

<style lang="scss" scoped>
.popup {
  font-size: 14px;

  &-button {
    position: fixed;
    right: 10px;
    display: flex;
    bottom: 20px;
    button {
      height: 35px;
      line-height: 35px;
      min-width: 90px;
      font-size: 16px;
      color: #fff;
    }
    &__reset {
      border-top-left-radius: 30px;
      border-bottom-left-radius: 30px;
      margin-right: 2px;
      background: linear-gradient(90deg, #fcc706, #fb9800);
    }
    &__sure {
      border-top-right-radius: 30px;
      border-bottom-right-radius: 30px;
      background: linear-gradient(90deg, #f97b14, #ff5008);
    }
  }
  // 行
  &-item {
    margin-top: 10px;
    &__tt {
      font-size: 14px;
      margin-bottom: 15px;
    }
    &__line {
      color: #999;
      margin: 0 10px;
    }
    &__price {
      display: flex;
      margin: 10px 0;
      align-items: center;
    }
    &__search {
      background: #f9f9f9;
      border-radius: 30px;
      max-width: 100px;
      padding: 0 15PX;
      font-size: 14px;
    }
    // 分类
    &__sort {
      display: flex;
      font-size: 12px;
    }
    &__sort > checkbox-group {
      display: inline-flex;
      margin-right: 5px;
      width: 25%;
      text-align: center;
      border-radius: 3px;
      box-sizing: border-box;
      text-align: center;
      display: block;
      position: relative;
      height: 30px;
    }
    &__sort input {
      display: none;
    }
    &__sort label {
      height: 30px;
      position: absolute;
      left: 0;
      background: #f9f9f9;
      width: 100%;
      height: 100%;
      top: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
  checkbox[checked] + label {
    background: rgba(255, 2, 4, 0.1);
    color: #ff0204;
  }
}

.lists {
  /deep/ .van-popup--right {
    width: 80%;
    height: 100vh;
    padding: 10px;
  }

  .g-flex {
    margin-bottom: $box-size;
    align-items: center;
    padding: 15px;
  }

  /deep/ .g-flex,
  /deep/ .goods-list__ft {
    background: #fff;
  }
  /deep/ .goods-list {
    padding: 0 5px;
  }
  /deep/ .search {
    flex: 1;
  }
  /deep/ .goods-list__img {
    background: #eee;
  }
}

.icon-filter {
  margin-left: 20px;
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0RkU3MDcyQUQxRDIxMUU4OUI1Q0QxRTY1QkY1NjMyMSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0RkU3MDcyQkQxRDIxMUU4OUI1Q0QxRTY1QkY1NjMyMSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRGRTcwNzI4RDFEMjExRTg5QjVDRDFFNjVCRjU2MzIxIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRGRTcwNzI5RDFEMjExRTg5QjVDRDFFNjVCRjU2MzIxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+V61PqgAAAIhJREFUeNrslssNgCAQRFnFirQ3bcHirMgYSMCD8YgJDuTNges+sp8ZCyE4ZQ1OXAB+lU/vOGnSXecNmKW2LdZQi7M2RUBLd1B4BjkzAAIIIIANWd0sxna8rU4vzbRgdc8WL6QZ0gyAAAIIYN9pZq1Ucy+1ulppxrqyOl/0s+pphi0G8D9FAQYA2m0h9C4C0qEAAAAASUVORK5CYII=)
    no-repeat;
  background-size: 20px 17px;
  width: 20px;
  height: 20px;
  display: inline-block;
}
</style>
