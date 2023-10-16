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
### 1. PyCharm 설치
<p align="center"><img width="750" alt="a" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/bcd6679c-db80-4a12-ad8d-8ae6a5d87f6b"></p>

<br/>

- AI 모델을 실행하기 위한 PyCharm Community Edition을 설치한다.

### 2. XAMPP 설치
<p align="center"><img width="500" alt="XAMPP" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/e7511884-a34b-43a8-81e1-4068e726871c"></p>

- 각자의 OS에 맞춰 XAMPP를 설치한다.
<p align="center"> <img width="500" alt="control panel2" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/7ab95789-1371-4c33-a28d-f8dc48c62456"></p>

- 빨간색 테두리로 표시된 버튼을 눌러 Apache와 mySql을 실행한다.
- 그 후, 연두색 테두리로 표시된 버튼을 눌러 phpMyAdmin DataBase로 이동한다.


<hr>
<p align="center"><img width="500" alt="DB생성" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/73a38140-3a70-4a08-9997-074b4a7c0dfb"></p>

- DB이름을 graduation_project 및 본인이 원하는 이름으로 설정한다.
- (이름 변경 시, PHP코드의 DB접속 부분을 변경된 이름에 맞춰 수정해야 한다.)
  
<p align="center"><img width="500" alt="테이블 디폴트" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/1f47f256-fc49-4791-8530-d5a450598a73"></p>

- 메일을 담을 mail 테이블을 생성한다.

<p align="center"><img width="500" alt="테이블 구조" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/2d356b65-ae28-49ec-a77d-ae8ad73811dc"></p>

- 속성들을 참고하여, CREATE 쿼리문 없이도 구현이 가능하다.
<hr>

### 3. 동작 방식

#### 1. Client - Side

<p align="center"><img width="940" alt="1" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/8cf69a74-2aba-4b21-8ac2-2bf58ce91b93"></p>

- 좌상단의 새로고침 버튼을 이용해 페이지를 다시 로드할 수 있다.
- 우상단의 홈 버튼을 이용해 어느 페이지에서도 홈페이지로 돌아올 수 있다.

<p align="center"><img width="940" alt="2" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/6e7717ca-556f-40ad-94ec-2c3bddb3b549"></p>

- 클라이언트가 머신러닝의 구현을 살펴볼 수 있는 분기문이다.

<p align="center"><img width="940" alt="3" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/5b44ba14-75e3-4b75-87ff-3c333b638113"></p>

- 사용자는 CHAT-GPT로 생성한 메일데이터 + 이미지, 유저가 직접 입력할 수 있는 메일 + 이미지나 단일 조합의 데이터를 전송할 수 있다.

  <p align="center"><img width="328" alt="14" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/edbcf1b8-9eb3-4159-a107-af63678aec82"></p>

- 유저 입력 데이터의 경우, 빨강 원 버튼을 눌러 음성으로 녹음이 가능하다.
- 한국어도 인식이 가능하지만 영어로 데이터가 들어가야 올바른 분석이 가능하다.

  <p align="center"><img width="897" alt="8" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/4ab1fc3c-b72a-4f09-8396-761b73351c2e"></p>

- 마우스 클릭으로 보낼 데이터를 선택할 수 있고, 선택 시 하단부에 출력여부를 알린다.

#### 2. Server - Side

<p align="center"><img width="500" alt="10" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/0e049b7a-e1e2-420a-a4f6-98e3bdfb28bf"></p>

- Polling 방식으로 구현된 스팸필터링 AI모델(Python)을 실행하여 메일분석을 대비한다.

<p align="center"><img width="500" alt="20" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/08aac6e3-98e3-4744-b539-be0e93488a97"></p>

- 입력된 메일의 분석결과가 Python 콘솔창에 출력된다.

<p align="center"><img width="940" alt="12" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/bcbec139-af16-491d-ba74-2928e831f2cb"></p>
<p align="center"><img width="940" alt="13" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/45bd9055-15ce-4c1f-a759-639a9cc55cdf"></p>

- 초록색 화살표를 이용하여 화면 전환이 가능하다.
- 입력 텍스트에서 스팸키워드와 입력 문자의 특성을 Pie-Chart와 Bar-Chart로 표현된다.
- 마우스를 위에 올리면 각각의 세부 개수를 알 수 있다.

#### DATABASE

<p align="center"><img width="936" alt="DB조회" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/68a03985-98d7-4088-bb32-f8d6d1569749"></p>

- DB조회 버튼을 눌러 지금까지 AI가 분석한 메일을 TABLE형식으로 조회 가능하다.
-  스팸여부에 따라 테두리가 다르게 표시된다. (스팸 - 빨강, 스팸이 아닌 것 - 초록)
  
<p align="center"><img width="939" alt="DB클릭" src="https://github.com/pnucse-capstone/capstone-2023-1-49/assets/99540674/4fb5ec5b-73f2-4828-88dc-03671cb2c7fc"></p>

- 각각의 컬럼을 클릭하면 메일 형식으로 조회가능하며 스팸키워드들을 하이라이팅하여 보여준다.


















