<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zagorodna</title>
</head>
<body style="margin:0; padding:0; font: 16px Arial,sans-serif; line-height: 24px; color: #3d3d3d;">
    <table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto; padding:40px 40px 20px; max-width: 600px; width: 100%; font: 16px Arial,sans-serif; line-height: 24px; color: #3d3d3d;">
    <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; color: #3d3d3d; padding: 0px 0px 15px; border-bottom: 4px solid #f1ebe4;">
                <img border="0" width="95" height="80" style="max-width:95px; max-height: 80px; display: inline-block; vertical-align: middle;" src="{{ url('img/email-img/logo.svg') }}" alt="logo">
                <span style="-webkit-text-size-adjust:none; font-size: 13px; margin: 0px; display: inline-block; line-height: 15px; vertical-align: middle; padding-left: 10px;">
                Портал загородной<br>недвижимости №1<br>в Украине</span>
            </td>
        </tr>
        
        <tr>
            <td style="font: 32px Arial,sans-serif; line-height: 34px; color: #3d3d3d; padding: 55px 0px 40px;">
                <b style="-webkit-text-size-adjust:none; font-size: 32px; margin: 0px; display: block; text-transform: uppercase; color: #3c2f21;">Привет от zagorodna!</b>
            </td>
        </tr>
        <tr>
            <td style="font: 22px Arial,sans-serif; line-height: 24px; color: #3d3d3d; padding: 0px 0px 30px;">
                <b style="-webkit-text-size-adjust:none; font-size: 22px; margin: 0px; display: block;">Обновления на Zagorodna за эту неделю</b>
            </td>
        </tr>
        <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; Margin: 0;margin: 0; padding-bottom: 20px;">
            Разнообразный и богатый опыт постоянное информационно-пропагандистское обеспечение нашей деятельности позволяет выполнять важные задания по разработке системы.
            </td>
        </tr>
        @foreach($notifications->where('type', 'new') as $item)
        <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; Margin: 0;margin: 0;">
                <span style=" display:inline-block; vertical-align: top; text-align: center; padding-right: 25px;">
                    <img border="0" width="180" height="90" style="max-width:180px; max-height: 90px; display:inline-block; object-fit: cover;" src="{{ url($item->product->image) }}" alt="{{ $item->product->name }}">
                </span>
                <span style=" display:inline-block; vertical-align: top; -webkit-text-size-adjust:none; font-size: 20px; line-height: 30px;">
                <b>
                    Новый объект 
                    <br>
                    {{ $item->product->name }}
                </b>
                </span>
            </td>
        </tr>
        <td style="Margin: 0;margin: 0;padding: 45px 0px 25px; text-align: center; font: 13px Arial,sans-serif;">
            <a href="{{ $item->product->link }}" target="_blank" style="color: #ffffff; -webkit-text-size-adjust:none; text-decoration: none; text-transform: uppercase; display: inline-block; width: 270px; height: 48px; line-height: 48px; text-align: center; background-color: #7bc97b; border-radius: 6px; box-shadow: 0px 1px 6px 0px rgba(0, 0, 0, 0.08); position: relative;">
                <b>посмотреть</b>
                <span style="position: absolute; right: 20px;top: -2px; font-size: 28px;">&#8594;</span>
            </a>
        </td>
        @endforeach
        @foreach($notifications->where('type', 'old') as $item)
        @if($item->old_status != $item->status)
        <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; Margin: 0;margin: 0;">
                <span style=" display:inline-block; vertical-align: top; text-align: center; padding-right: 25px;">
                    <img border="0" width="180" height="90" style="max-width:180px; max-height: 90px; display:inline-block; object-fit: cover;" src="{{ url($item->product->image) }}" alt="{{ $item->product->name }}">
                </span>
                <span style=" display:inline-block; vertical-align: top; -webkit-text-size-adjust:none; font-size: 20px; line-height: 30px;">
                <b>
                    Изменение статуса 
                    <br>
                    {{ $item->product->name }}
                    <br>
                    {{ __('main.product_statuses.' . $item->old_status) }} > 
                    {{ __('main.product_statuses.' . $item->status) }}
                </b>
                </span>
            </td>
        </tr>
        <td style="Margin: 0;margin: 0;padding: 45px 0px 25px; text-align: center; font: 13px Arial,sans-serif;">
            <a href="{{ $item->product->link }}" target="_blank" style="color: #ffffff; -webkit-text-size-adjust:none; text-decoration: none; text-transform: uppercase; display: inline-block; width: 270px; height: 48px; line-height: 48px; text-align: center; background-color: #7bc97b; border-radius: 6px; box-shadow: 0px 1px 6px 0px rgba(0, 0, 0, 0.08); position: relative;">
                <b>посмотреть</b>
                <span style="position: absolute; right: 20px;top: -2px; font-size: 28px;">&#8594;</span>
            </a>
        </td>
        @endif
        @endforeach
        @foreach($notifications->where('type', 'old') as $item)
        @if($item->old_price != $item->price)
        <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; Margin: 0;margin: 0;">
                <span style=" display:inline-block; vertical-align: top; text-align: center; padding-right: 25px;">
                    <img border="0" width="180" height="90" style="max-width:180px; max-height: 90px; display:inline-block; object-fit: cover;" src="{{ url($item->product->image) }}" alt="{{ $item->product->name }}">
                </span>
                <span style=" display:inline-block; vertical-align: top; -webkit-text-size-adjust:none; font-size: 20px; line-height: 30px;">
                <b>
                    Изменение цены 
                    <br>
                    {{ $item->product->name }}
                    <br>
                    {{ $item->old_price }} > 
                    {{ $item->price }} грн/кв.м
                </b>
                </span>
            </td>
        </tr>
        <td style="Margin: 0;margin: 0;padding: 45px 0px 25px; text-align: center; font: 13px Arial,sans-serif;">
            <a href="{{ $item->product->link }}" target="_blank" style="color: #ffffff; -webkit-text-size-adjust:none; text-decoration: none; text-transform: uppercase; display: inline-block; width: 270px; height: 48px; line-height: 48px; text-align: center; background-color: #7bc97b; border-radius: 6px; box-shadow: 0px 1px 6px 0px rgba(0, 0, 0, 0.08); position: relative;">
                <b>посмотреть</b>
                <span style="position: absolute; right: 20px;top: -2px; font-size: 28px;">&#8594;</span>
            </a>
        </td>
        @endif
        @endforeach
        <tr>
            <td style="font: 16px Arial,sans-serif; line-height: 24px; Margin: 0;margin: 0; padding-top: 40px;">
                <table border="0" cellpadding="0" cellspacing="0" style="overflow: auto;max-width: 320px; width: 100%; margin:0 auto; text-align: center;">
                    <tr>
                        <td colspan="5" style="Margin: 0;margin: 0;padding: 0px 0px 10px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px;">
                            <a href="tel:+38 050-384-44-98" target="_blank" style="color: #575757; -webkit-text-size-adjust:none; text-decoration: none;"><b>+38 050-384-44-98</b></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="Margin: 0;margin: 0;padding: 0px 0px 12px; text-align: center; font: 12px Arial,sans-serif; line-height: 14px;">
                            <span style="color: #575757; -webkit-text-size-adjust:none;">
                                Пн.-Пт.: с 10.00 до 18.00<br>
                                Выходные: суббота, воскресенье
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="Margin: 0;margin: 0;padding: 0px 0px 20px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px;">
                            <a href="mailto:admin@zagorodna.com" target="_blank" style="color: #575757; -webkit-text-size-adjust:none; text-decoration: none;"><b>admin@zagorodna.com</b></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="Margin: 0;margin: 0;padding-right: 10px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px; max-width: 48px; width: 48px;">
                            <a href="#" target="_blank" style=" -webkit-text-size-adjust:none; text-decoration: none; display: inline-block; width: 48px; height: 48px; background-color: #f2f2f2; border-radius: 5px; line-height: 54px;">
                                <img width="18" height="18" style="max-height: 18px; max-width: 18px;" src="{{ url('img/email-img/facebook.svg') }}" alt="Facebook">
                            </a>
                        </td>
                        <td style="Margin: 0;margin: 0;padding-right: 10px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px; max-width: 48px; width: 48px;">
                            <a href="#" target="_blank" style=" -webkit-text-size-adjust:none; text-decoration: none; display: inline-block; width: 48px; height: 48px; background-color: #f2f2f2; border-radius: 5px; line-height: 54px;">
                                <img width="18" height="18" style="max-height: 18px; max-width: 18px;" src="{{ url('img/email-img/twitter.svg') }}" alt="Twitter">
                            </a>
                        </td>
                        <td style="Margin: 0;margin: 0;padding-right: 10px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px; max-width: 48px; width: 48px;">
                            <a href="#" target="_blank" style=" -webkit-text-size-adjust:none; text-decoration: none; display: inline-block; width: 48px; height: 48px; background-color: #f2f2f2; border-radius: 5px; line-height: 54px;">
                                <img width="18" height="18" style="max-height: 18px; max-width: 18px;" src="{{ url('img/email-img/instagram.svg') }}" alt="Instagram">
                            </a>
                        </td>
                        <td style="Margin: 0;margin: 0;padding-right: 10px; text-align: center; font: 16px Arial,sans-serif; line-height: 24px; max-width: 48px; width: 48px;">
                            <a href="#" target="_blank" style=" -webkit-text-size-adjust:none; text-decoration: none; display: inline-block; width: 48px; height: 48px; background-color: #f2f2f2; border-radius: 5px; line-height: 54px;">
                                <img width="18" height="18" style="max-height: 18px; max-width: 18px;" src="{{ url('img/email-img/youtube.svg') }}" alt="youtube">
                            </a>
                        </td>
                        <td style="Margin: 0;margin: 0; text-align: center; font: 16px Arial,sans-serif; line-height: 24px; max-width: 48px; width: 48px;">
                            <a href="#" target="_blank" style=" -webkit-text-size-adjust:none; text-decoration: none; display: inline-block; width: 48px; height: 48px; background-color: #f2f2f2; border-radius: 5px; line-height: 54px;">
                                <img width="18" height="18" style="max-height: 18px; max-width: 18px;" src="{{ url('img/email-img/vk.svg') }}" alt="vkontakte">
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="Margin: 0;margin: 0;padding: 35px 0px 0px; text-align: center; font: 12px Arial,sans-serif; line-height: 24px;">
                <a href="#" target="_blank" style="color: #949494; -webkit-text-size-adjust:none; padding-right: 30px;">Отписаться от уведомлений по этому объекту</a>
                <a href="#" target="_blank" style="color: #949494; -webkit-text-size-adjust:none;">Отписаться от рассылки</a>
            </td>
        </tr>
        <tr>
            <td style="Margin: 0;margin: 0;padding: 25px 0px 0px; text-align: center; font: 12px Arial,sans-serif; line-height: 24px;">
                <span style="color: #949494; -webkit-text-size-adjust:none; text-decoration: none;">© Copyright 2010-2020 "РеалЭкспо" ООО</span>
            </td>
        </tr>
    </table>
</body>
</html>