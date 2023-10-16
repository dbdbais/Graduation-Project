# 🎉2023년 전기 졸업과제 49조
-----------
## Github classroom
[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-24ddc0f5d75046c5622901739e7c5dd533143b0c8e959d652212380cedb1ea36.svg)](https://classroom.github.com/a/fnZ3vxy8)
--------
## 💻 프로젝트 소개
- 프로젝트 명
  - 멀티모달을 활용한 텍스트 기반 스팸필터링 플랫폼 개발
- 목적
  - 멀티모달 딥러닝/머신러닝 스팸 필터링 플랫폼 개발을 목적으로 한다.
- 개요
  - 수집 텍스트 스팸 데이터 셋(kaggle), 생성 텍스트 스팸 데이터 셋(chat-gpt), 
이미지 스팸 데이터 셋(spam-Archive)의 3 가지 모달리티를 활용한다.
  - 각 모달리티만의 특징을 분석하여 이를 기반으로 모델을 학습시킨다.
  - 스팸 필터링 모델과 DB, Web을 연동하여 시각화 인터페이스를 구현한다.
  - 새로운 유형, 다양한 방식(이미지와 텍스트가 같이 오는 경우 등)의 스팸 메일에 대처에 유연한 스팸 필터링 모델을 개발한다.
## ☀️ 팀 소개
- **윤상호**
  - 이메일 : ehfhfjd@pusan.ac.kr
  - 역할 : 데이터 수집 및 정리, 데이터 생성, 전처리(중복제거, 토큰화, 품사처리), 분석용 feature 15개 생성, feature 분석, 다중/단일 입력 스팸 필터링 모델 개발, 멀티모달 스팸필터링 모델 개발
- **조재홍**
  - 이메일 : wjjh1221@pusan.ac.kr
  - 역할 : 데이터 수집 및 정리, 데이터 생성, 전처리(중복제거, 토큰화, 품사처리), 분석용 feature 15개 생성, feature 분석, 다중/단일 입력 스팸 필터링 모델 개발, 멀티모달 스팸필터링 모델 개발
- **이강우**
  - 이메일 : rain5191@pusan.ac.kr 
  - 역할 : 데이터 수집 및 정리, 데이터 생성, 클라이언트 사이드로 웹페이지 구현, 서버 사이드로 DB 구축, 서버와 웹 AI 모델과 연동.

## 🚴 구성도
![image](https://github.com/pnucse-capstone/capstone-2023-1-49/assets/100823955/5ce1ce97-8a2b-4421-a66c-5173358a1f5c)
1. 사용자(정상, 스패머)가 메일을 작성하여 다른 사용자(희생자)에게 전송한다.
2. 메일(텍스트, 이미지, 혼합 등)이 Multi-Modal 기반 스팸 필터링 모델에 의해 필터링.
3. 실시간으로 웹에서 필터링 결과를 출력한다.
## 📹 소개 및 시연 영상
↓ 아래 썸네일 클릭.\
[![시연영상 유튜브 링크](http://img.youtube.com/vi/G_m9r8yo7nI/0.jpg)](https://youtu.be/G_m9r8yo7nI)
## 📄 사용법
### ⚙️ 개발 환경
- `window 10`
- `python 3.9.5`
- **Library** : `keras`, `tensorflow`, `sklearn`, `nltk`, `KoNLPy`
- **Framework** : `PyCharm 2022.01.03`, `xampp 8.2.4`
- **Database** : `MySQL Workbench 8.0 CE`
### 🔑 설치 및 실행 방법
