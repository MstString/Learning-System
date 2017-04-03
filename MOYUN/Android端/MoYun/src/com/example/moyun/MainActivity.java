package com.example.moyun;

import android.app.Activity;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Window;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class MainActivity extends Activity {
    
        private static final String TAG = MainActivity.class.getSimpleName();
    
        private String errorHtml = "";
        WebView mWebView;
    
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_main);
        errorHtml = "<html><body><h1>Page not find!</h1></body></html>";
        mWebView = (WebView) findViewById(R.id.web_view);
        mWebView.getSettings().setJavaScriptEnabled(true);
        
        mWebView.loadUrl("http://115.159.91.223/");
        Log.i(TAG, "--onCreate--");
        
        //设置web视图的客户端
        mWebView.setWebViewClient(new MyWebViewClient());
    }
    
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if ((keyCode == KeyEvent.KEYCODE_BACK) && mWebView.canGoBack()) {
            mWebView.goBack(); // goBack()表示返回WebView的上一页面
            return true;
        }
        return super.onKeyDown(keyCode,event);
    }
    
    @Override
    protected void onResume() {
     
            super.onResume();
               Log.i(TAG, "--onResume()--");
    }
     
    public class MyWebViewClient extends WebViewClient{
            
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                      Log.i(TAG, "-MyWebViewClient->shouldOverrideUrlLoading()--");
                     view.loadUrl(url);
                    return true;
            }
            
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                      Log.i(TAG, "-MyWebViewClient->onPageStarted()--");
                    super.onPageStarted(view, url, favicon);
            }
            
            @Override
            public void onPageFinished(WebView view, String url) {
                      Log.i(TAG, "-MyWebViewClient->onPageFinished()--");
                    super.onPageFinished(view, url);
            }
            
            
            @Override
            public void onReceivedError(WebView view, int errorCode,
                            String description, String failingUrl) {
                    super.onReceivedError(view, errorCode, description, failingUrl);
                    
                      Log.i(TAG, "-MyWebViewClient->onReceivedError()--\n errorCode="+errorCode+" \ndescription="+description+" \nfailingUrl="+failingUrl);
                     //这里进行无网络或错误处理，具体可以根据errorCode的值进行判断，做跟详细的处理。
//                              view.loadData("file:///android_asset/error.html", "text/html", "UTF-8");
                      view.loadUrl("file:///android_asset/error.html");
                     
                     
            }
    }
}
