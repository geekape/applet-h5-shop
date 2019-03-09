<template>
  <div class="address white-bg">
    <navbar text="我的地址"></navbar>
    <scroller >
    <van-address-edit
      :area-list="areaList"
      show-search-result
      :address-info="addressInfo"
      @save="saveAddress"
    />
    </scroller>
  </div>
</template>

<script>
import {post,get,wx,data} from "@/utils"
import {Toast} from "vant"
import navbar from "@/components/navbar";
export default {
  data () {
    return {
      name: '',
      moblie: '',
      addressArea: '所在地区',
      customItem:[],
      type: 0,
      areaList: data,
      addressInfo: {}
    }
  },
  computed: {
  },

  components: {navbar},

  methods: {
    saveAddress (e) {
      console.log(e)
      
      let obj = {
        PHPSESSID: window.localStorage.getItem('PHPSESSID'),
        truename: e.name,
        mobile: e.tel,
        address: `${e.province},${e.city},${e.county}`,
        address_detail: e.addressDetail,
        is_use: 1,
        is_choose: 1
      }
      post("/shop/api/add_address", obj)
      .then((res) => {
        Toast('保存地址成功')
        // 从订单页进来的
        if(this.type == 1) {
          window.history.go(-1)
          
        } else {
         this.$router.push({path:'/center'})
        }
        obj.areaCode = e.areaCode
				
        window.localStorage.setItem('address', JSON.stringify(obj))
      })
      }
  },

  created () {
    const _this = this
    this.type = this.$route.params.type || 0
    let address = JSON.parse(window.localStorage.getItem('address'))
    
    if(address) {
      let area = address.address.split(',')
      _this.addressInfo = {
        name: address.truename,
        tel: address.mobile,
        addressDetail: address.address_detail,
        areaCode:address.areaCode
      }
    } else {
      get("/shop/api/add_address/PHPSESSID/"+ window.localStorage.getItem('PHPSESSID'))
      .then((res) => {
        let address = res
        _this.addressInfo = {
          name: res.info.truename,
          tel:  res.info.mobile,
          addressDetail: res.info.address_detail,
          areaCode: res.info.areaCode || 110101
        }
      })
    }
    

   
    
  }
}
</script>

<style>
page {
  background: #fff!important;
}
</style>

<style lang="scss" scoped>
  .address {
    /deep/ .van-address-edit {margin-top: 15px;}
    /deep/ .van-button {
      background: $gradient;
      border-radius: 30px;
      border: 0;
    }
    /deep/ .van-cell {background: transparent;}
  }
</style>
