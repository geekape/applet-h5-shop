import { post, get } from './index'
const _SESSID = wx.getStorageSync('PHPSESSID')
const api = {
  // 获取会员卡
  getCard: () => post('card/api/me', {
    PHPSESSID: _SESSID
  }),
  // 获取验证码
  getVerifyCode: (phone) => post('card/api/send_sms_code', {
    PHPSESSID: _SESSID,
    to: Number(phone)
  }),
  // 会员通知
  getInform: () => post('card/api/member_inform', {
    PHPSESSID: _SESSID
  }),
  // 会员有礼
  getGiftInfo: () => post('card/api/custom_privilege', {
    PHPSESSID: _SESSID
  }),
  // 积分兑换
  getScoreExchange: () => post('card/api/score_exchange', {
    PHPSESSID: _SESSID
  }),
  // 会员中心
  getMemberCenter: () => post('card/api/member_center', {
    PHPSESSID: _SESSID
  }),
  // 积分攻略
  getScorePsp: () => post('card/api/score_method', {
    PHPSESSID: _SESSID
  }),
  // 签到记录
  getSignRecord: () => post('card/api/sign_list', {
    PHPSESSID: _SESSID
  }),
  // 签到页
  getSigninInfo: () => post('card/api/signin', {
    PHPSESSID: _SESSID
  }),
  // 签到
  goSignin: () => post('card/api/do_signin', {
    PHPSESSID: _SESSID
  }),
  // 获取会员信息
  getMemberInfo: () => post('card/api/complete_cardinfo', {
    PHPSESSID: _SESSID
  }),
  // 保存会员信息
  saveMemberInfo: (opt) => post('card/api/save_member', opt),

}

export {
  api
}