<script>
const common = require("@/utils/common");
import { host } from "@/utils";
export default {
 
  data() {
    return {
      PHPSESSID: "",
      common: common
    };
  },
  onLaunch(options) {
    var from_uid =
      options.query.from_uid == undefined ? "0" : options.query.from_uid;
    if (from_uid != 0) {
      wx.setStorageSync("from_uid", from_uid);
    }

    common.initApp(host + "weiapp/Api/", true);
  }
};
</script>

<style lang="scss">
@import "../static/styles/base.scss";

/* this rule will be remove */
* {
  transition: width 2s;
  -moz-transition: width 2s;
  -webkit-transition: width 2s;
  -o-transition: width 2s;
}
</style>
