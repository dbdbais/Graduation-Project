import mysql.connector
import time
import json
# 정규표현식 적용을 위한 라이브러리 호출
# Load required libraries
import tensorflow as tf
import numpy as np
import pandas as pd
import sys
from keras.models import load_model
sys.setrecursionlimit(1500)

from sklearn.preprocessing import LabelEncoder
from keras.preprocessing.text import Tokenizer
import pad_sequences
from keras.models import Model
from keras.layers import Input, Embedding, Dense, concatenate, Flatten
from keras.callbacks import ModelCheckpoint, TensorBoard
from keras.models import Sequential
from keras.layers import Dense, Activation, Dropout
from sklearn.model_selection import train_test_split

import sklearn
from sklearn import metrics
import joblib

import re
from nltk.tokenize import TweetTokenizer

import nltk
from konlpy.tag import Twitter
from nltk.tokenize import word_tokenize
from nltk.tag import pos_tag
from collections import Counter
from nltk.tokenize import sent_tokenize
# nltk 데이터 다운로드 (한 번 실행 후 주석 처리해도 됨)
nltk.download('punkt')
nltk.download('averaged_perceptron_tagger')

def token_count(row):
    text=row['tokenized_text']
    length=len(str(text).split())
    return length

def tokenize(row, text):
    NEWLINE = '\n'
    text=row[text]
    lines=(line for line in str(text).split(NEWLINE))
    tokenized=""
    for sentence in lines:
        tokenized+= " ".join(tok for tok in sentence.split())
    return tokenized

# 정규표현식을 이용하여 특수문자의 비율을 반환하는 함수 정의
def ratio_special_chars(text):
    if len(text) > 0:
        special_chars = re.findall(r'[^\w\s]', text)
        return len(special_chars) / len(text)
    else:
        return 0.0
# Min-Max 정규화 함수 정의
def minmax_normalize(column):
    min_val = column.min()
    max_val = column.max()
    normalized_column = (column - min_val) / (max_val - min_val)
    return normalized_column

# 숫자 비율 반환 함수 정의
def ratio_numbers(text):
    if len(text) > 0:
        pattern = r'\d+'  # 숫자를 찾기 위한 정규 표현식
        numbers = re.findall(pattern, text)
        return len(numbers) / len(text)
    else:
        return 0.0

# 문장 내에 URL 카운팅하는 함수 정의
def url_count(tokens):
    count = 0
    for token in tokens:
        if re.match(r"(http\S+|www\.\S+)", token):
            count = count + 1
    return count

# 대문자의 비율을 반환하는 함수를 정의
def ratio_upper(text):
    if len(text) > 0:
        return len(re.findall(r'[A-Z]', text)) / len(text)
    else:
        return 0.0

# 공백의 개수를 카운팅하는 함수를 정의합니다.
def ratio_blank(text):
    if len(text) > 0:
        return text.count(' ') / len(text)
    else:
        return 0.0
# 개행문자의 비율을 반환하는 함수 정의
def ratio_crlf(text):
    if len(text) > 0:
        crlf_count = text.count('\n')
        return crlf_count / len(text)
    else:
        return 0.0

# NLTK 품사 태깅 함수
def pos_tagging(text):
    words = word_tokenize(text)
    tagged_words = pos_tag(words)
    return tagged_words

# 품사별 비율 계산 함수
def calculate_pos_ratio(tagged_text):
    total_words = len(tagged_text)
    pos_counts = Counter(tag for word, tag in tagged_text)
    pos_ratios = {pos: count / total_words for pos, count in pos_counts.items()}
    return pos_ratios

# 문장 단위로 나누는 함수
def split_into_sentences(text):
    sentences = sent_tokenize(text)
    return sentences

# 평균 단어 수 계산 함수
def calculate_avg_word_count(sentences):
    if len(text) > 0:
        total_word_count = sum(len(word_tokenize(sentence)) for sentence in sentences)
        avg_word_count = total_word_count / len(sentences)
        return avg_word_count
    else:
        return 0.0

# 평균 글자 수 계산 함수
def calculate_avg_char_count(sentences):
    if len(text) > 0:
        total_char_count = sum(len(sentence) for sentence in sentences)
        avg_char_count = total_char_count / len(sentences)
        return avg_char_count
    else:
        return 0.0

def train_tf_idf_model(texts):
    tic = time.process_time()
    tok = Tokenizer(num_words=num_max)
    tok.fit_on_texts(texts)
    toc = time.process_time()
    return tok

def prepare_model_input(tfidf_model, dataframe, mode='tfidf'):
    tic = time.process_time()
    le = LabelEncoder()
    sample_texts = list(dataframe['tokenized_text'])
    sample_texts = [' '.join(x.split()) for x in sample_texts]
    targets = list(dataframe['label'])
    targets = [1. if x == 'spam' else 0. for x in targets]
    sample_target = le.fit_transform(targets)

    if mode == 'tfidf':
        sample_texts = tfidf_model.texts_to_matrix(sample_texts, mode='tfidf')
    else:
        sample_texts = tfidf_model.texts_to_matrix(sample_texts)
    toc = time.process_time()
    return sample_texts, sample_target

def get_simple_model():
    model = Sequential()
    model.add(Dense(512, activation='relu', input_shape=(num_max,)))
    model.add(Dropout(0.5))
    model.add(Dense(256, activation='relu'))
    model.add(Dropout(0.5))
    model.add(Dense(1, activation='sigmoid'))
    model.summary()
    model.compile(loss='binary_crossentropy',
              optimizer='adam',
              metrics=['acc',tf.keras.metrics.binary_accuracy])
    return model

def check_model2(model,x_train,y_train,x_val,y_val,epochs=10):
    history=model.fit(x_train,y_train,batch_size=64,
                      epochs=epochs,verbose=1,
                      shuffle=True,
                      validation_data=(x_val, y_val),
                      callbacks=[checkpointer, tensorboard]).history
    return history

def get_generate_image_model():
    # 은닉층과 출력층 설정
    hidden_layer = Dense(64, activation='relu')(generate_image_merged_input)
    output_layer = Dense(1, activation='sigmoid', name='output')(hidden_layer)
    # 모델 생성
    model = Model(inputs=[generate_image_text_input, generate_image_number_ratio_input,
                          generate_image_upper_ratio_input, generate_image_crlf_ratio_input,
                          generate_image_Pronoun_input, generate_image_Adjective_input],
                  outputs=output_layer)
    # 모델 요약
    model.summary()
    # 모델 컴파일
    model.compile(optimizer='adam', loss='binary_crossentropy', metrics=['accuracy'])
    return model

def get_collect_image_model():
    # 은닉층과 출력층 설정
    hidden_layer = Dense(64, activation='relu')(collect_image_merged_input)
    output_layer = Dense(1, activation='sigmoid', name='output')(hidden_layer)
    # 모델 생성
    model = Model(inputs=[collect_image_text_input, collect_image_upper_ratio_input, collect_image_blank_ratio_input,
                          collect_image_Noun_input, collect_image_Pronoun_input], outputs=output_layer)
    # 모델 요약
    model.summary()
    # 모델 컴파일
    model.compile(optimizer='adam', loss='binary_crossentropy', metrics=['accuracy'])
    return model

def detect_row_additions(connection, table_name, last_id):
    query = f"SELECT * FROM {table_name} WHERE idx > {last_id}"

    cursor = connection.cursor(dictionary=True)
    cursor.execute(query)

    new_rows = []
    for row in cursor:
        new_rows.append(row)

    cursor.close()
    return new_rows


def get_max_id(cursor, table_name):  # db의 MAXID 가져옴
    cursor.execute(f"SELECT MAX(idx) FROM {table_name}")
    return cursor.fetchone()[0] or 0

def preprocess(df, text):
    df['tokenized_text'] = df.apply(lambda row: tokenize(row, text), axis=1)
    df['token_count'] = df.apply(token_count, axis=1)
    df['lang'] = 'en'
    df['special_ratio'] = df[text].apply(ratio_special_chars)
    # df['special_ratio'] = minmax_normalize(df['special_ratio'])
    df['number_ratio'] = df[text].apply(ratio_numbers)
    # df['number_ratio'] = minmax_normalize(df['number_ratio'])
    tweet_tokenizer = TweetTokenizer()
    df['tokens'] = df[text].apply(tweet_tokenizer.tokenize)
    df['url_count'] = df['tokens'].apply(url_count)
    df['upper_ratio'] = df[text].apply(ratio_upper)
    # df['upper_ratio'] = minmax_normalize(df['upper_ratio'])
    df['blank_ratio'] = df[text].apply(ratio_blank)
    # df['blank_ratio'] = minmax_normalize(df['blank_ratio'])
    df['crlf_ratio'] = df[text].apply(ratio_crlf)
    # df['crlf_ratio'] = minmax_normalize(df['crlf_ratio'])
    df['pos_tagged'] = df[text].apply(pos_tagging)

    i = 0
    lst_final = []

    for tags in df['pos_tagged']:
        new_tags = []
        for word, pos_tag in tags:
            if pos_tag in ['NN', 'NNS', 'NNP', 'NNPS', 'VBG']:
                new_tags.append((word, 'Noun'))
            elif pos_tag in ['PRP', 'PRP$', 'WP', 'WP$']:
                new_tags.append((word, 'Pronoun'))
            elif pos_tag in ['MD', 'VB', 'VBD', 'VBN', 'VBP', 'VBZ']:
                new_tags.append((word, 'Verb'))
            elif pos_tag in ['JJ', 'JJR', 'JJS']:
                new_tags.append((word, 'Adjective'))
            elif pos_tag in ['RB', 'RBR', 'RBS', 'WRB']:
                new_tags.append((word, 'Adverb'))
            else:
                new_tags.append((word, pos_tag))
        lst_final.append(new_tags)
        i += 1
    df['modified_pos_tagged'] = lst_final
    df['pos_ratios'] = df['modified_pos_tagged'].apply(calculate_pos_ratio)
    l1 = []
    for d in df['pos_ratios']:
        l1.append(d.get('Noun', 0))

    df['Noun'] = pd.DataFrame(l1)

    l2 = []
    for d in df['pos_ratios']:
        l2.append(d.get('Pronoun', 0))

    df['Pronoun'] = pd.DataFrame(l2)

    l3 = []
    for d in df['pos_ratios']:
        l3.append(d.get('Verb', 0))

    df['Verb'] = pd.DataFrame(l3)

    l4 = []
    for d in df['pos_ratios']:
        l4.append(d.get('Adjective', 0))

    df['Adjective'] = pd.DataFrame(l4)

    l5 = []
    for d in df['pos_ratios']:
        l5.append(d.get('Adverb', 0))

    df['Adverb'] = pd.DataFrame(l5)

    df['sentences'] = df[text].apply(split_into_sentences)
    df['avg_word_sentences'] = df['sentences'].apply(calculate_avg_word_count)
    # df['avg_word_sentences'] = minmax_normalize(df['avg_word_sentences'])
    df['avg_char_sentences'] = df['sentences'].apply(calculate_avg_char_count)
    # df['avg_char_sentences'] = minmax_normalize(df['avg_char_sentences'])

    # 텍스트를 문단 단위로 분할하여 문단 당 평균 단어 수와 평균 글자 수 계산
    paragraphs = df[text].str.split('\n\n')
    avg_word_lengths = []
    avg_char_lengths = []

    for paragraph in paragraphs:
        word_count = 0
        char_count = 0
        for sentence in paragraph:
            words = sentence.split()
            word_count += len(words)
            char_count += sum(len(word) for word in words)

        if len(paragraph) == 0:
            avg_word_lengths.append(0)
            avg_char_lengths.append(0)
            break
        avg_word_lengths.append(word_count / len(paragraph))
        avg_char_lengths.append(char_count / len(paragraph))

    # 새로운 칼럼으로 추가
    df['avg_word_paragraphs'] = avg_word_lengths
    df['avg_char_paragraphs'] = avg_char_lengths
    # df['avg_word_paragraphs'] = minmax_normalize(df['avg_word_paragraphs'])
    # df['avg_char_paragraphs'] = minmax_normalize(df['avg_char_paragraphs'])

    return df


if __name__ == "__main__":

    host = "127.0.0.1"
    user = "root"
    password = "dlrkddn1@"
    database = "graduation_project"
    table = "mail"

    # Establish a connection
    connection = mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database
    )

    t = "text"
    # 모델 돌리기
    # collect learning
    df_collect_model = pd.read_csv('C:\collect_train_1.csv')
    df_collect_model['tokenized_text'] = df_collect_model.apply(lambda row: tokenize(row, t), axis=1)
    df_collect_model['token_count'] = df_collect_model.apply(token_count, axis=1)
    df_collect_model['lang'] = 'en'
    num_max = 4000
    collect_texts = list(df_collect_model['tokenized_text'])
    collect_tfidf_model = train_tf_idf_model(collect_texts)
    collect_mat_texts, collect_tags = prepare_model_input(collect_tfidf_model, df_collect_model, mode='tfidf')
    collect_X_train, collect_X_val, collect_y_train, collect_y_val = train_test_split(collect_mat_texts,
                                                                                      collect_tags, test_size=0.15,
                                                                                      random_state=42)

    # generate learning
    df_generate_model = pd.read_csv('C:\create_train_1.csv')
    df_generate_model['tokenized_text'] = df_generate_model.apply(lambda row: tokenize(row, t), axis=1)
    df_generate_model['token_count'] = df_generate_model.apply(token_count, axis=1)
    df_generate_model['lang'] = 'en'
    num_max = 200
    generate_texts = list(df_generate_model['tokenized_text'])
    generate_tfidf_model = train_tf_idf_model(generate_texts)
    generate_mat_texts, generate_tags = prepare_model_input(generate_tfidf_model, df_generate_model, mode='tfidf')
    generate_X_train, generate_X_val, generate_y_train, generate_y_val = train_test_split(generate_mat_texts,
                                                                                          generate_tags, test_size=0.15,
                                                                                          random_state=42)

    # image learning
    df_image_model = pd.read_csv('C:\image_train_1.csv')
    df_image_model['tokenized_text'] = df_image_model.apply(lambda row: tokenize(row, t), axis=1)
    df_image_model['token_count'] = df_image_model.apply(token_count, axis=1)
    df_image_model['lang'] = 'en'
    num_max = 600
    image_texts = list(df_image_model['tokenized_text'])
    image_tfidf_model = train_tf_idf_model(image_texts)
    image_mat_texts, image_tags = prepare_model_input(image_tfidf_model, df_image_model, mode='tfidf')
    image_X_train, image_X_val, image_y_train, image_y_val = train_test_split(image_mat_texts, image_tags,
                                                                              test_size=0.15, random_state=42)

    # generate+image learning
    df_generate_image_model = pd.read_csv('C:\createimage_train_16features.csv')
    df_generate_image_model['tokenized_text'] = df_generate_image_model.apply(lambda row: tokenize(row, t), axis=1)
    df_generate_image_model['token_count'] = df_generate_image_model.apply(token_count, axis=1)
    df_generate_image_model['lang'] = 'en'
    num_max = 800
    generate_image_texts = list(df_generate_image_model['tokenized_text'])
    generate_image_tfidf_model = train_tf_idf_model(generate_image_texts)
    generate_image_mat_texts, generate_image_tags = prepare_model_input(generate_image_tfidf_model,
                                                                        df_generate_image_model, mode='tfidf')

    # generate_image_special_ratio = np.array(df_model['special_ratio'])
    generate_image_number_ratio = np.array(df_generate_image_model['number_ratio'])
    # generate_image_url_count = np.array(df_generate_image_model['url_count'])
    generate_image_upper_ratio = np.array(df_generate_image_model['upper_ratio'])
    # generate_image_blank_ratio = np.array(df_generate_image_model['blank_ratio'])
    generate_image_crlf_ratio = np.array(df_generate_image_model['crlf_ratio'])
    # generate_image_Noun = np.array(df_generate_image_model['Noun'])
    generate_image_Pronoun = np.array(df_generate_image_model['Pronoun'])
    # generate_image_Verb = np.array(df_generate_image_model['Verb'])
    generate_image_Adjective = np.array(df_generate_image_model['Adjective'])
    # generate_image_Adverb = np.array(df_generate_image_model['Adverb'])
    # generate_image_avg_word_sentences = np.array(df_generate_image_model['avg_word_sentences'])
    # generate_image_avg_char_sentences = np.array(df_generate_image_model['avg_char_sentences'])
    # generate_image_avg_word_paragraphs = np.array(df_generate_image_model['avg_word_paragraphs'])
    # generate_image_avg_char_paragraphs = np.array(df_generate_image_model['avg_char_paragraphs'])

    generate_image_text_input = Input(shape=(num_max,), name='generate_image_text_input')
    # generate_image_special_ratio_input = Input(shape=(1,), name='special_ratio_input')
    generate_image_number_ratio_input = Input(shape=(1,), name='generate_image_number_ratio_input')
    # generate_image_url_count_input = Input(shape=(1,), name='generate_image_url_count_input')
    generate_image_upper_ratio_input = Input(shape=(1,), name='generate_image_upper_ratio_input')
    # generate_image_blank_ratio_input = Input(shape=(1,), name='generate_image_blank_ratio_input')
    generate_image_crlf_ratio_input = Input(shape=(1,), name='generate_image_crlf_ratio_input')
    # generate_image_Noun_input = Input(shape=(1,), name='generate_image_Noun_input')
    generate_image_Pronoun_input = Input(shape=(1,), name='generate_image_Pronoun_input')
    # generate_image_Verb_input = Input(shape=(1,), name='generate_image_Verb_input')
    generate_image_Adjective_input = Input(shape=(1,), name='generate_image_Adjective_input')
    # generate_image_Adverb_input = Input(shape=(1,), name='generate_image_Adverb_input')
    # generate_image_avg_word_sentences_input = Input(shape=(1,), name='generate_image_avg_word_sentences_input')
    # generate_image_avg_char_sentences_input = Input(shape=(1,), name='generate_image_avg_char_sentences_input')
    # generate_image_avg_word_paragraphs_input = Input(shape=(1,), name='generate_image_avg_word_paragraphs_input')
    # generate_image_avg_char_paragraphs_input = Input(shape=(1,), name='generate_image_avg_char_paragraphs_input')

    generate_image_merged_input = concatenate(
        [generate_image_text_input, generate_image_number_ratio_input, generate_image_upper_ratio_input,
         generate_image_crlf_ratio_input, generate_image_Pronoun_input, generate_image_Adjective_input])

    (generate_image_text_train, generate_image_text_val, generate_image_number_ratio_train,
     generate_image_number_ratio_val, generate_image_upper_ratio_train, generate_image_upper_ratio_val,
     generate_image_crlf_ratio_train, generate_image_crlf_ratio_val, generate_image_Pronoun_train,
     generate_image_Pronoun_val, generate_image_Adjective_train, generate_image_Adjective_val,
     generate_image_tags_train, generate_image_tags_val) = train_test_split(
        generate_image_mat_texts, generate_image_number_ratio, generate_image_upper_ratio, generate_image_crlf_ratio,
        generate_image_Pronoun, generate_image_Adjective, generate_image_tags, test_size=0.15, random_state=42)


    # collect+image learning
    df_collect_image_model = pd.read_csv('C:\collectimage_train_16features.csv')
    df_collect_image_model['tokenized_text'] = df_collect_image_model.apply(lambda row: tokenize(row, t), axis=1)
    df_collect_image_model['token_count'] = df_collect_image_model.apply(token_count, axis=1)
    df_collect_image_model['lang'] = 'en'
    num_max = 4000
    collect_image_texts = list(df_collect_image_model['tokenized_text'])
    collect_image_tfidf_model = train_tf_idf_model(collect_image_texts)
    collect_image_mat_texts, collect_image_tags = prepare_model_input(collect_image_tfidf_model, df_collect_image_model,
                                                                      mode='tfidf')

    # collect_image_special_ratio = np.array(df_collect_image_model['special_ratio'])
    # collect_image_number_ratio = np.array(df_collect_image_model['number_ratio'])
    # collect_image_url_count = np.array(df_collect_image_model['url_count'])
    collect_image_upper_ratio = np.array(df_collect_image_model['upper_ratio'])
    collect_image_blank_ratio = np.array(df_collect_image_model['blank_ratio'])
    # collect_image_crlf_ratio = np.array(df_collect_image_model['crlf_ratio'])
    collect_image_Noun = np.array(df_collect_image_model['Noun'])
    collect_image_Pronoun = np.array(df_collect_image_model['Pronoun'])
    # collect_image_Verb = np.array(df_collect_image_model['Verb'])
    # collect_image_Adjective = np.array(df_collect_image_model['Adjective'])
    # collect_image_Adverb = np.array(df_collect_image_model['Adverb'])
    # collect_image_avg_word_sentences = np.array(df_collect_image_model['avg_word_sentences'])
    # collect_image_avg_char_sentences = np.array(df_collect_image_model['avg_char_sentences'])
    # collect_image_avg_word_paragraphs = np.array(df_collect_image_model['avg_word_paragraphs'])
    # collect_image_avg_char_paragraphs = np.array(df_collect_image_model['avg_char_paragraphs'])

    collect_image_text_input = Input(shape=(num_max,), name='collect_image_text_input')

    # 특수문자, 숫자 개수, URL 개수 입력 레이어
    # collect_image_special_ratio_input = Input(shape=(1,), name='collect_image_special_ratio_input')
    # collect_image_number_ratio_input = Input(shape=(1,), name='collect_image_number_ratio_input')
    # collect_image_url_count_input = Input(shape=(1,), name='collect_image_url_count_input')
    collect_image_upper_ratio_input = Input(shape=(1,), name='collect_image_upper_ratio_input')
    collect_image_blank_ratio_input = Input(shape=(1,), name='collect_image_blank_ratio_input')
    # collect_image_crlf_ratio_input = Input(shape=(1,), name='collect_image_crlf_ratio_input')
    collect_image_Noun_input = Input(shape=(1,), name='collect_image_Noun_input')
    collect_image_Pronoun_input = Input(shape=(1,), name='collect_image_Pronoun_input')
    # collect_image_Verb_input = Input(shape=(1,), name='collect_image_Verb_input')
    # collect_image_Adjective_input = Input(shape=(1,), name='collect_image_Adjective_input')
    # collect_image_Adverb_input = Input(shape=(1,), name='collect_image_Adverb_input')
    # collect_image_avg_word_sentences_input = Input(shape=(1,), name='collect_image_avg_word_sentences_input')
    # collect_image_avg_char_sentences_input = Input(shape=(1,), name='collect_image_avg_char_sentences_input')
    # collect_image_avg_word_paragraphs_input = Input(shape=(1,), name='collect_image_avg_word_paragraphs_input')
    # collect_image_avg_char_paragraphs_input = Input(shape=(1,), name='collect_image_avg_char_paragraphs_input')

    collect_image_merged_input = concatenate([collect_image_text_input, collect_image_upper_ratio_input,
                                              collect_image_blank_ratio_input, collect_image_Noun_input,
                                              collect_image_Pronoun_input])
    (collect_image_text_train, collect_image_text_val, collect_image_upper_ratio_train, collect_image_upper_ratio_val,
     collect_image_blank_ratio_train, collect_image_blank_ratio_val, collect_image_Noun_train, collect_image_Noun_val,
     collect_image_Pronoun_train, collect_image_Pronoun_val, collect_image_tags_train, collect_image_tags_val) = \
        train_test_split(collect_image_mat_texts, collect_image_upper_ratio, collect_image_blank_ratio,
                         collect_image_Noun, collect_image_Pronoun, collect_image_tags, test_size=0.15, random_state=42)

    collect_model = load_model('C:/Users/newbi/PycharmProjects/pythonProject1/checkpoints/Final_collect_model.h5')
    generate_model = load_model('C:/Users/newbi/PycharmProjects/pythonProject1/checkpoints/Final_generate_model.h5')
    image_model = load_model('C:/Users/newbi/PycharmProjects/pythonProject1/checkpoints/Final_image_model.h5')
    generate_image_model = load_model(
        'C:/Users/newbi/PycharmProjects/pythonProject1/checkpoints/Final_generateimage_model.h5')
    collect_image_model = load_model(
        'C:/Users/newbi/PycharmProjects/pythonProject1/checkpoints/Final_collectimage_model.h5')
    print("Learning Done")


    result_collect = 0
    result_generate = 0
    result_image = 0
    result_collect_image = 0
    result_generate_image = 0
    try:
        cursor = connection.cursor()  # DB와 파이썬 연결
        last_id = get_max_id(cursor, table)  # 현재 가장 큰 idx가져옴
        while True:
            new_rows = detect_row_additions(connection, table, last_id)  # 새로운 행 있다면 받아옴
            if new_rows:
                print("New rows added:")
                for row in new_rows:
                    print(row)  # 콘솔 창에 새로 입력된 row 출력
                collect_image_lst = []
                generate_image_lst = []
                temp = list(new_rows)
                df = pd.DataFrame(temp)

                collect_image_lst.append(df['collect'] + df['img'])
                generate_image_lst.append(df['generate'] + df['img'])

                df['collect_image'] = pd.DataFrame(collect_image_lst)
                df['generate_image'] = pd.DataFrame(generate_image_lst)

                text = ""
                if df.iloc[0]['label'] == 0:
                    text = 'collect'
                    df_collect = preprocess(df, text)
                    collect_sample_texts, collect_sample_target = prepare_model_input(collect_tfidf_model, df_collect,
                                                                                      mode='')
                    df_final = df_collect
                    result_collect = collect_model.predict(collect_sample_texts)
                    #result_collect = result_collect.flatten()
                elif df.iloc[0]['label'] == 1:
                    text = 'generate'
                    df_generate = preprocess(df, text)
                    generate_sample_texts, generate_sample_target = prepare_model_input(generate_tfidf_model,
                                                                                        df_generate, mode='')
                    df_final = df_generate
                    result_generate = generate_model.predict(generate_sample_texts)
                    #result_generate = result_generate.flatten()
                elif df.iloc[0]['label'] == 2:
                    text = 'img'
                    df_image = preprocess(df, text)
                    image_sample_texts, image_sample_target = prepare_model_input(image_tfidf_model, df_image, mode='')
                    df_final = df_image
                    result_image = image_model.predict(image_sample_texts)
                    #result_image = result_image.flatten()
                elif df.iloc[0]['label'] == 3:
                    text = 'collect'
                    df_collect = preprocess(df, text)
                    text = 'img'
                    df_image = preprocess(df, text)
                    text = 'collect_image'
                    df_collect_image = preprocess(df, text)

                    collect_image_sample_texts, collect_image_sample_target = prepare_model_input(
                        collect_image_tfidf_model, df_collect_image, mode='')
                    collect_sample_texts, collect_sample_target = prepare_model_input(collect_tfidf_model, df_collect,
                                                                                      mode='')
                    image_sample_texts, image_sample_target = prepare_model_input(image_tfidf_model, df_image, mode='')

                    special_ratio_test = np.array(df_collect_image['special_ratio'])
                    number_ratio_test = np.array(df_collect_image['number_ratio'])
                    url_count_test = np.array(df_collect_image['url_count'])
                    upper_ratio_test = np.array(df_collect_image['upper_ratio'])
                    blank_ratio_test = np.array(df_collect_image['blank_ratio'])
                    crlf_ratio_test = np.array(df_collect_image['crlf_ratio'])
                    Noun_test = np.array(df_collect_image['Noun'])
                    Pronoun_test = np.array(df_collect_image['Pronoun'])
                    Verb_test = np.array(df_collect_image['Verb'])
                    Adjective_test = np.array(df_collect_image['Adjective'])
                    Adverb_test = np.array(df_collect_image['Adverb'])
                    avg_word_sentences_test = np.array(df_collect_image['avg_word_sentences'])
                    avg_char_sentences_test = np.array(df_collect_image['avg_char_sentences'])
                    avg_word_paragraphs_test = np.array(df_collect_image['avg_word_paragraphs'])
                    avg_char_paragraphs_test = np.array(df_collect_image['avg_char_paragraphs'])

                    df_final = df_collect_image

                    lst_result_collect_image = []
                    input_data = [collect_image_sample_texts, upper_ratio_test, blank_ratio_test,
                                  Noun_test, Pronoun_test]

                    result_collect_image = collect_image_model.predict(input_data)
                    result_collect = collect_model.predict(collect_sample_texts)
                    result_image = image_model.predict(image_sample_texts)

                    #result_collect = result_collect.flatten()
                    #result_image = result_image.flatten()
                    #result_collect_image = result_collect_image.flatten()
                else:
                    text = 'generate'
                    df_generate = preprocess(df, text)
                    text = 'img'
                    df_image = preprocess(df, text)
                    text = 'generate_image'
                    df_generate_image = preprocess(df, text)

                    generate_image_sample_texts, generate_image_sample_target = prepare_model_input(
                        generate_image_tfidf_model, df_generate_image, mode='')
                    generate_sample_texts, generate_sample_target = prepare_model_input(generate_tfidf_model,
                                                                                        df_generate, mode='')
                    image_sample_texts, image_sample_target = prepare_model_input(image_tfidf_model, df_image, mode='')

                    special_ratio_test = np.array(df_generate_image['special_ratio'])
                    number_ratio_test = np.array(df_generate_image['number_ratio'])
                    url_count_test = np.array(df_generate_image['url_count'])
                    upper_ratio_test = np.array(df_generate_image['upper_ratio'])
                    blank_ratio_test = np.array(df_generate_image['blank_ratio'])
                    crlf_ratio_test = np.array(df_generate_image['crlf_ratio'])
                    Noun_test = np.array(df_generate_image['Noun'])
                    Pronoun_test = np.array(df_generate_image['Pronoun'])
                    Verb_test = np.array(df_generate_image['Verb'])
                    Adjective_test = np.array(df_generate_image['Adjective'])
                    Adverb_test = np.array(df_generate_image['Adverb'])
                    avg_word_sentences_test = np.array(df_generate_image['avg_word_sentences'])
                    avg_char_sentences_test = np.array(df_generate_image['avg_char_sentences'])
                    avg_word_paragraphs_test = np.array(df_generate_image['avg_word_paragraphs'])
                    avg_char_paragraphs_test = np.array(df_generate_image['avg_char_paragraphs'])

                    df_final = df_generate_image

                    lst_result_generate_image = []
                    input_data = [generate_image_sample_texts, number_ratio_test, upper_ratio_test,
                                  crlf_ratio_test, Pronoun_test, Adjective_test]

                    # Predict using the model
                    result_generate_image = generate_image_model.predict(input_data)

                    result_generate = generate_model.predict(generate_sample_texts)
                    result_image = image_model.predict(image_sample_texts)
                    #result_generate = result_collect.flatten()
                    #result_image = result_image.flatten()
                    #result_generate_image = result_generate_image.flatten()

                text_value = df_final.iloc[0][text]
                special_value = df_final.iloc[0]['special_ratio']
                number_value = df_final.iloc[0]['number_ratio']
                url_value = df_final.iloc[0]['url_count']
                upper_value = df_final.iloc[0]['upper_ratio']
                blank_value = df_final.iloc[0]['blank_ratio']
                crlf_value = df_final.iloc[0]['crlf_ratio']
                noun_value = df_final.iloc[0]['Noun']
                pronoun_value = df_final.iloc[0]['Pronoun']
                verb_value = df_final.iloc[0]['Verb']
                adjective_value = df_final.iloc[0]['Adjective']
                adverb_value = df_final.iloc[0]['Adverb']
                word_sentences_value = df_final.iloc[0]['avg_word_sentences']
                char_sentences_value = df_final.iloc[0]['avg_char_sentences']
                word_paragraphs_value = df_final.iloc[0]['avg_word_paragraphs']
                char_paragraphs_value = df_final.iloc[0]['avg_char_paragraphs']
                print("Info :", df_final.iloc[0])

                len_text = len(str(df.iloc[0][text]))
                lst_feature = []
                lst_feature.append(int(special_value*len_text))
                lst_feature.append(int(number_value*len_text))
                lst_feature.append(int(url_value))
                lst_feature.append(int(upper_value*len_text))
                lst_feature.append(int(blank_value*len_text))
                lst_feature.append(int(crlf_value*len_text))
                lst_feature.append(int(noun_value*len_text))
                lst_feature.append(int(pronoun_value*len_text))
                lst_feature.append(int(verb_value*len_text))
                lst_feature.append(int(adjective_value*len_text))
                lst_feature.append(int(adverb_value*len_text))
                lst_feature.append(int(word_sentences_value))
                lst_feature.append(int(char_sentences_value))
                lst_feature.append(int(word_paragraphs_value))
                lst_feature.append(int(char_paragraphs_value))

                print("List: ", lst_feature)

                print("생성 예측값")
                print(result_generate)
                print("수집 예측값")
                print(result_collect)
                print("이미지 예측값")
                print(result_image)
                print("수집+이미지 예측값")
                print(result_collect_image)
                print("생성+이미지 예측값")
                print(result_generate_image)

                if result_collect > 0.5:
                    result_collect = 1
                else:
                    result_collect = 0
                if result_generate > 0.5:
                    result_generate = 1
                else:
                    result_generate = 0
                if result_image > 0.5:
                    result_image = 1
                else:
                    result_image = 0
                if result_collect_image > 0.5:
                    result_collect_image = 1
                else:
                    result_collect_image = 0
                if result_generate_image > 0.5:
                    result_generate_image = 1
                else:
                    result_generate_image = 0

                # concatenate
                if df.iloc[0]['label'] == 3:  # collect + image
                    lst_result_collect_image.append(result_collect)
                    lst_result_collect_image.append(result_image)
                    lst_result_collect_image.append(result_collect_image)
                elif df.iloc[0]['label'] == 4:  # generate + image
                    lst_result_generate_image.append(result_generate)
                    lst_result_generate_image.append(result_image)
                    lst_result_generate_image.append(result_generate_image)

                result = 0

                if df.iloc[0]['label'] == 0: # collect
                    result = result_collect
                elif df.iloc[0]['label'] == 1: #generate
                    result = result_generate
                elif df.iloc[0]['label'] == 2: # image
                    result = result_image
                elif df.iloc[0]['label'] == 3: # collect + image
                    if lst_result_collect_image.count(1) >= 2:
                        result = 1
                    elif lst_result_collect_image.count(0) >= 2:
                        result = 0
                elif df.iloc[0]['label'] == 4: # generate + image
                    if lst_result_generate_image.count(1) >= 2:
                        result = 1
                    elif lst_result_generate_image.count(0) >= 2:
                        result = 0
                last_id = max(row['idx'] for row in new_rows)  # last_id 최신으로 업데이트
                lst_json = json.dumps(lst_feature)  # 리스트를 JSON 형식으로 변환
                print(lst_feature)
                print("SPAM: ", result)
                query = f"UPDATE mail SET predict = %s, feature = %s WHERE idx = %s"
                data = (result, lst_json, last_id)
                cursor.execute(query, data)

            time.sleep(2)  # 2초 간격으로 Polling
            connection.commit()  # 실시간 db 연동 (중요!) 빼지 말 것
    except KeyboardInterrupt:
        pass
    finally:
        connection.close()  # 연결 끊음