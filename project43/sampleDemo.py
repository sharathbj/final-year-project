#!/usr/bin/env python
import re
import csv
import pprint
import nltk.classify

#start replaceTwoOrMore
def replaceTwoOrMore(s):
    
    pattern = re.compile(r"(.)\1{1,}", re.DOTALL) 
    return pattern.sub(r"\1\1", s)
#end

#start process_comment
def processComment(comment):
    comment = comment.lower()
    comment = re.sub('((www\.[^\s]+)|(https?://[^\s]+))','URL',comment)
    comment = re.sub('[\s]+', ' ', comment)
    return comment
#end 

#start getStopWordList
def getStopWordList(stopWordListFileName):
    #read the stopwords
    stopWords = []
    stopWords.append('URL')

    fp = open(stopWordListFileName, 'r')
    line = fp.readline()
    while line:
        word = line.strip()
        stopWords.append(word)
        line = fp.readline()
    fp.close()
    return stopWords
#end

#start getfeatureVector
def getFeatureVector(comment, stopWords):
    featureVector = []  
    words = comment.split()
    for w in words:
        #replace two or more with two occurrences 
        w = replaceTwoOrMore(w) 
        #strip punctuation
        w = w.strip('\'"?,.')
        #check if it consists of only words
        val = re.search(r"^[a-zA-Z][a-zA-Z0-9]*[a-zA-Z]+[a-zA-Z0-9]*$", w)
        #ignore if it is a stopWord
        if(w in stopWords or val is None):
            continue
        else:
            featureVector.append(w.lower())
    return featureVector    
#end

#start extract_features
def extract_features(comment):
    comment_words = set(comment)
    features = {}
    for word in featureList:
        features['contains(%s)' % word] = (word in comment_words)
    return features
#end

#**********Actually program start here************
trainingComments = csv.reader(open('data/trainingdata.csv', 'rb'), delimiter=',', quotechar='|')
stopWords = getStopWordList('data/feature_list/stopwords.txt')
count = 0;
featureList = []
comments = []

for row in trainingComments:
    sentiment = row[0]
    comment = row[1]
    processedComment = processComment(comment)
    featureVector = getFeatureVector(processedComment, stopWords)
    featureList.extend(featureVector)
    comments.append((featureVector, sentiment));    
#end loop

featureList = list(set(featureList))

training_set = nltk.classify.util.apply_features(extract_features, comments)
NBClassifier = nltk.NaiveBayesClassifier.train(training_set)

# Test the classifier
inpRealComments = csv.reader(open('/home/onemore/Downloads/commentTable.csv', 'rb'))
with open('/home/onemore/Downloads/Finaloutput.csv','wb') as outfile:
    write=csv.writer(outfile)
    
    for realcomment in inpRealComments:
        count+=1 
        print ' comment NO=%d'%(count)
        c0= realcomment[0]
        c1= realcomment[1]
        c2= realcomment[2]
        c3= realcomment[3]      
        testComment= realcomment[4]

        processedTestComment = processComment(testComment)
        #print processedTestComment
        print getFeatureVector(processedTestComment, stopWords)
        #print extract_features(getFeatureVector(processedTestComment, stopWords))
        sentiment = NBClassifier.classify(extract_features(getFeatureVector(processedTestComment, stopWords)))
        print "sentiment = %s\n" % (sentiment)
        write.writerow([c0,c1,c2,c3,testComment,sentiment])