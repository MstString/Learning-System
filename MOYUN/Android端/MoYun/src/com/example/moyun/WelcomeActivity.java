package com.example.moyun;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.Window;

public class WelcomeActivity extends Activity{

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.activity_welcome);
		
		
		Handler handler = new Handler();
		//��ʱ��������תҳ��
		handler.postDelayed(new Runnable(){
			@Override
			public void run(){
				Intent intent = new Intent(WelcomeActivity.this,MainActivity.class);
				startActivity(intent);
				WelcomeActivity.this.finish();
			}
		},2000);
		/*new Thread (new Runnable(){
			@Override
			public void run() {
				//�˴����к�ʱ����
				runOnUiThread(new Runnable() {
					@Override
					public void run() {
						Intent intent = new Intent(WelcomeActivity.this,MainActivity.class);
						startActivity(intent);
						WelcomeActivity.this.finish();
					}
				});
			}
		}).start();*/
	}

}
