## Form Building Guide


### Introduction

This document describes the Simple Form Language (SFL), this is a basic syntax to describe a conversational form. This syntax can be used to create forms that require a step-by-step fulfilment of an action, like a form on a webpage. In this form builder you can create different flows for the user to give a personal experience. The form builder is not used for describing the main flow of the chatbot. An example of a use case for this kind of form could be a conversation that is collecting a name and email address from the user to subscribe to a newsletter.



### Basic Form Syntax

The basic syntax of a question and an answer consists of a minimum of two lines, the header and the question. After these lines you can add the expected input and you can also add an error message, which are optional. We will be going over each of these lines in this document.

The syntax is based on a specific order. The header comes first, followed by the bot's question. If a user input is expected, you can add it beneath the question which is then followed by the error message. The order is important, the indententation not so much, but makes the code a lot clearer.



```
1.	Header
2.		Question
3.			[Input.Intent.Redirect] (Optional but used frequently)
4.				Error (Optional)
```





#### Header

Each question requires a header, this is a name for your question or block of questions. The header should fit the content of the question and is easily recognizable. The header is case sensitive and can be used to redirect the user to a specific question for example, we will discuss the redirect function later on in this document.



```
1.	welcome
2.		x
3.			x
4.				x
```





#### Question

On this line you will write the sentence the bot will say to the user. This sentence will most of the times be a question, like asking for a name, though it is not necessarily a question.



```
1.	welcome
2.		What is your name?
3.			x
4.				x
```

Not everything the bot says requires an input, to link multiple questions you will need to write the sentences on a new line. The bot will go over all the sentences one by one. In this next example, the bot will say a sentence that doesn't require an input, like "I will be your assistant today.'' followed by a question that does require an input "What is your name?". Both sentences will get their own speech bubble.



```
1.	welcome
2.		I will be your assistant today.
3.		What is your name?
4.			x
5.				x
```





#### Input

Each question requires a user input, this input can vary from a simple sentence to an email address or just a press of a button. The user input consists of three main parts, saying it's a user input, the expected user intent and a redirect. The syntax for the user input is as follows



```
1.	welcome
2.		I will be your assistant today.
3.		What is your name?
4.			[Input.<Your_Intent>.<Your_Redirect>]
5.				x
```





#### Intent

For almost every question the bot expects an answer, you can tell the bot what to expect by giving the input a certain intent. The most basic intent is Input.any, with this intent the user can say anything and the bot will always continue. There are many more intents:

| Trigger                           | Explanation                                                  | Example             |
| :-------------------------------- | :----------------------------------------------------------- | ------------------- |
| Input.any                         | Triggers on any message. It doesn't matter what the user says. | Bla Bla             |
| Input.button(&lt;Button_Tag&gt;)        | Triggers if a button has been pressed, there can be multiple buttons | Yes - No            |
| Input.name                     | Triggers if the user's message contains a name             | My name is Hassan     |
| Input.date                     | Triggers if the user's message contains a date             | Today is the fifth of june     |
| Input.variables(&lt;variable list header&gt;)                     | Triggers if the user's message contains a variable from the CMS variables list             | I'd like to pay with PayPal     |
| Input.numeric                     | Triggers if the user's message contains a number             | My number is 31     |
| Input.zipcode                     | Triggers on dutch zipcode's                                  | 1059AT              |
| Input.email                       | Triggers on email adresses                                   | John.doe@server.com |
| Input.telephone                   | Triggers on dutch telephone numbers                          | 06 123 456 78       |
| Input.iban                        | Triggers on International Bank Account Numbers (IBAN's).     | NL37INGB0005544332  |
| Input.exact(&lt;Your_String&gt;)        | Matches if user's message is exactly the provided string (case-insensitive). | Yes                 |
| Input.match(&lt;regular_expression&gt;) | Matches on provided regular expression.                      | Hello World         |


&nbsp;


An answer can also consist of multiple intents, for example, if you want to give the user a choice, you can use two buttons with each their own name and redirect location. The button has a specific input that requires a title for the button: `[Input.button(Title).X]`. Here is an example on how to create multiple buttons:



```
1.	welcome
2.		I will be your assistant today.
3.		What is your name?
4.			[Input.button(Yes).X][Input.button(Nope).X]
5.				x
```



Here the user gets a choice to click on the "Yes" or "Nope" button. you can give each button their own redirect marked as X in the example.





#### Redirect

Redirecting the user to a specific question can be valuable for creating a dynamic flow. If a user answers question A you may want to send them to B or maybe even to Y. You can choose where to redirect the user after you have given the Intent. A redirect is optional, the bot will just go over all the questions if you don't define a specific redirect.

In the redirect you give the header of the question you want to redirect the user to, for example:



```
1.	welcome
2.		I will be your assistant today.
3.		What is your name?
4.			[Input.button(Yes).hello][Input.button(Nope).cancel]
5.				x
```



Now, if the user presses the button 'Yes', he will be redirected to the question with the 'hello' header and if the user presses 'No', he will be redirected to the 'cancel' header. This way you can really define the flow of the conversation.



A question or sentence can also redirect the user directly without requiring an input, in that case you leave out `<Input> & <Intent>` and go straight to the redirect:



```plain
1.	welcome
2.		I will be your assistant today, let's start.
3.			[redirect.gettingstarted]
4.
5.	gettingstarted
6.		First off, I would like to know your name, what is your name?
```


#### Error (optional)

If you have defined a specific Intent for the user, `[Input.email]` for example, you expect the answer to be an email address, if the user does not give an email address as an answer, you would want to give an error message. This can easily be done by adding the error underneath the input, for example:



```
1.	welcome
2.		I will be your assistant today.
3.		What is your name?
4.			[Input.email.validemail]
5.				Please enter a valid email address
```



This way, if a user does not give a valid email address, the bot will give the following error message "This is not a valid email address, please enter your email address.". The error message is optional, if the bot expects any input "[Input.any]", you would not want to enter an error message, as every answer would be fine. If the user gives a fitting answer, he or she will be redirected to header 'validemail'.





#### Remember Input

Sometimes if a user gives an input, you want the bot to remember that input to use later on. If you ask the user for a name, you may want to remember that name to greet him or her. This can be done by using the curly braces. After the intent you can put the title of the variable in curly braces to let the bot remember that answer.



In this example the bot asks the user for a name, and remembers that name. In the next question, the bot personally greets the user with that same name variable.



```
1.	welcome
2.		Hello and welcome, what is your name?
3.			[Input.any{name}]
4.
5.	hello
6.		Nice to meet you, {name}, do you want to get started?
7.			[Input.button(Yes).getstarted][Input.button(No).stop]
```



The bot can remember the input from every form of intent, like an email address or a telephone number.





#### Grouped Questions

When you give the user a choice, you sometimes want to create a certain flow based on the given input. If the user says A, you maybe don't want the following questions to be B, C and D but H, I and J instead. For this you can group questions and create a small sub-flow. To do this you create the sub-flow with a forward slash followed by the title of the sub-flow. To end the sub-flow you place two forward slashes. To redirect the user to the sub-flow, you just create a regular redirect with a forward slash next to it. Sub-flows will be skipped if not redirected to that specific flow.



```
1.	welcome
2.		Hello and welcome, do you want flow A or B?
3.			[Input.button(Flow A)./flowa][Input.button(Flow B)./flowb]
4.
5.	/flowa
6.
7.		firstnameA
8.			So you have chosen flow A, what is your name?
9.				[Input.any]
10.
11.		lastnameA
12.			And what is your last name?
13.				[Input.any]
14.
15.	//
16.
17.	/flowb
18.
19.		firstnameB
20.			So you have chosen flow B, what is your name?
21.				[Input.any]
22.
23.		lastnameB
24.			And what is your last name?
25.				[Input.any]
26.
27.	//
28.
29.	thankyou
30.		The were all the questions for today, thank you.
```



In the example above we can see that the bot gives the user a choice, if you press the button "Flow A", you go though the /flowa flow, if you select the "Flow B" button, you go through the /flowb flow. If either flow is finished, you will continue on the main flow,  "thankyou" in this case.

When the user has chosen flow A and finished the flow, the user will skip over Flow B and continue over the main flow.
