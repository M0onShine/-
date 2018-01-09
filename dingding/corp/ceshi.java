import hashlib
import time
import uuid
import json
import urllib
from tornado.httpclient import AsyncHTTPClient
from tornado import web
from tornado import gen
from base import BaseHandler


class DingDingBaseHandler(BaseHandler):

    CORP_ID = 'dinga612a9259fdd64c035c2f4657eb6378f'
    CORP_SECRET = 'lR60apDeFlELmbn1aJ1GCkfplf9lS2rlhRi4qbn1FPRlsaLva29r3Z8l_r9btzPE'
    AGENT_ID = '107977877'

    TOKEN_URL = 'https://oapi.dingtalk.com/gettoken'
    TICKET_URL = 'https://oapi.dingtalk.com/get_jsapi_ticket'
    USER_INFO_URL = 'https://oapi.dingtalk.com/user/getuserinfo'


    @gen.engine
    def get_user_info_by_code(self, code, callback):
        '通过前端 jsapi 得到的 code 获取当前用户信息'

        token = yield gen.Task(self.get_token)

        p = {
            'access_token': token,
            'code': code,
        }
        url = self.USER_INFO_URL + '?' + urllib.urlencode(p)
        resposne = yield gen.Task(AsyncHTTPClient().fetch, url)
        callback(json.loads(resposne.body))


    def sign(self, ticket, url):
        'jsapi 需要的签名'

        stamp = int(time.time())
        nonce = uuid.uuid4().hex
        p = {
            'noncestr': nonce,
            'jsapi_ticket': ticket.encode('utf8'),
            'timestamp': str(stamp),
            'url': url,
        }

        keys = p.keys()
        keys.sort()
        pair = [ (k, p[k]) for k in keys]
        pair = '&'.join('{}={}'.format(*p) for p in pair)
        sign = hashlib.sha1(pair).hexdigest()
        return sign, stamp, nonce


    @gen.engine
    def get_ticket(self, token, callback):
        '通过 access_token 获取 jsapi_ticket'

        p = {
            'access_token': token
        }

        url = self.TICKET_URL + '?' + urllib.urlencode(p)
        resposne = yield gen.Task(AsyncHTTPClient().fetch, url)
        ticket = json.loads(resposne.body)['ticket']
        callback(ticket)


    @gen.engine
    def get_token(self, callback):
        '能过 corp_id 和 corp_secret 获取 access_token'

        p = {
            'corpid': self.CORP_ID,
            'corpsecret': self.CORP_SECRET,
        }

        url = self.TOKEN_URL + '?' + urllib.urlencode(p)
        resposne = yield gen.Task(AsyncHTTPClient().fetch, url)
        token = json.loads(resposne.body)['access_token']
        callback(token)




class DingDingHandler(DingDingBaseHandler):
    def get(self):
        self.redirect('/dingding/login')


class DingDingJsapiSignHandler(DingDingBaseHandler):

    @web.asynchronous
    @gen.engine
    def get(self):
        '获取指定页面的钉钉 jsapi 的签名'

        url = self.get_argument('url', '')

        token = yield gen.Task(self.get_token)
        ticket = yield gen.Task(self.get_ticket, token)

        sign, stamp, nonce = self.sign(ticket, url)

        data = {
            'agent_id': self.AGENT_ID,
            'corp_id': self.CORP_ID,
            'timestamp': stamp,
            'nonce': nonce,
            'sign': sign,
        }

        self.finish({'code': 0, 'obj': data})


class DingDingUserInfoHandler(DingDingBaseHandler):

    @web.asynchronous
    @gen.engine
    def get(self):
        '通过 code 获取当前用户信息'
        code = self.get_argument('code', '')
        obj = yield gen.Task(self.get_user_info_by_code, code)
        self.finish({'code': 0, 'obj': obj})


class DingDingLoginHandler(DingDingBaseHandler):

    def get(self):
        self.render('index_ceshi.html')